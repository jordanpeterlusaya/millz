#!/usr/bin/env python3
"""MILLZ GAMES local admin — no database.

Serves the store and saves catalog + covers to files on this machine.

  python3 admin-server.py

Then open:
  http://127.0.0.1:8765/admin.html
"""

from __future__ import annotations

import base64
import json
import re
import secrets
import socket
import time
from http.server import SimpleHTTPRequestHandler, ThreadingHTTPServer
from pathlib import Path
from urllib.parse import urlparse

ROOT = Path(__file__).resolve().parent
CATALOG = ROOT / "data" / "catalog.json"
CODES = ROOT / "data" / "codes.json"
COVERS = ROOT / "assets" / "img" / "games"
ADMIN_CODE = "MILLZ005"
PREFERRED_PORT = 8765
CODE_TTL_MS = 4 * 60 * 60 * 1000


def is_local(handler: SimpleHTTPRequestHandler) -> bool:
    host = handler.client_address[0]
    return host in ("127.0.0.1", "::1", "localhost")


def check_admin(handler: SimpleHTTPRequestHandler) -> bool:
    key = handler.headers.get("X-Admin-Key", "")
    return is_local(handler) and key == ADMIN_CODE


def send_json(handler: SimpleHTTPRequestHandler, payload: dict, status: int = 200) -> None:
    body = json.dumps(payload).encode("utf-8")
    handler.send_response(status)
    handler.send_header("Content-Type", "application/json; charset=utf-8")
    handler.send_header("Cache-Control", "no-store")
    handler.send_header("Content-Length", str(len(body)))
    handler.end_headers()
    handler.wfile.write(body)


def read_body(handler: SimpleHTTPRequestHandler) -> bytes:
    length = int(handler.headers.get("Content-Length") or 0)
    if length <= 0:
        return b""
    if length > 8 * 1024 * 1024:
        return b""
    return handler.rfile.read(length)


def slug(value: str) -> str:
    text = re.sub(r"[^a-z0-9]+", "-", (value or "cover").lower()).strip("-")
    return (text or "cover")[:40]


class Handler(SimpleHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, directory=str(ROOT), **kwargs)

    def log_message(self, fmt: str, *args) -> None:
        print("[%s] %s" % (self.log_date_time_string(), fmt % args))

    def do_GET(self) -> None:
        path = urlparse(self.path).path
        if path == "/api/status":
            send_json(
                self,
                {
                    "local": True,
                    "disk": True,
                    "admin": "/admin.html",
                },
            )
            return
        if path == "/api/codes":
            self.list_codes()
            return
        super().do_GET()

    def do_POST(self) -> None:
        path = urlparse(self.path).path
        if path == "/api/catalog":
            self.save_catalog()
            return
        if path == "/api/cover":
            self.save_cover()
            return
        if path == "/api/codes":
            self.issue_code()
            return
        if path == "/api/codes/redeem":
            self.redeem_code()
            return
        self.send_error(404, "Not found")

    def list_codes(self) -> None:
        if not check_admin(self):
            send_json(self, {"ok": False, "error": "Admin code si sahihi au si localhost."}, 403)
            return
        send_json(self, {"ok": True, "codes": load_codes()})

    def issue_code(self) -> None:
        if not check_admin(self):
            send_json(self, {"ok": False, "error": "Admin code si sahihi au si localhost."}, 403)
            return
        try:
            payload = json.loads(read_body(self).decode("utf-8") or "{}")
        except Exception:
            send_json(self, {"ok": False, "error": "JSON si sahihi."}, 400)
            return
        game_id = str(payload.get("gameId") or "").strip()
        game = next((item for item in catalog_items() if str(item.get("id") or "") == game_id), None)
        if not game:
            send_json(self, {"ok": False, "error": "missing_game", "message": "Game haijapatikana."}, 404)
            return
        codes = load_codes()
        created = now_ms()
        entry = {
            "id": "code-%s-%s" % (created, game_id[:8]),
            "code": make_code(codes),
            "gameId": game_id,
            "gameName": game.get("name") or "Game",
            "createdAt": created,
            "expiresAt": created + CODE_TTL_MS,
            "used": False,
            "unlockedAt": None,
        }
        codes.insert(0, entry)
        save_codes(codes)
        send_json(self, {"ok": True, "disk": True, "code": entry["code"], "entry": entry})

    def redeem_code(self) -> None:
        try:
            payload = json.loads(read_body(self).decode("utf-8") or "{}")
        except Exception:
            send_json(self, {"ok": False, "error": "JSON si sahihi."}, 400)
            return
        code = normalize_code(str(payload.get("code") or ""))
        if not code:
            send_json(self, {"ok": False, "error": "invalid", "message": "Weka kodi uliyopewa na admin baada ya malipo."}, 400)
            return
        codes = load_codes()
        entry = next((item for item in codes if normalize_code(str(item.get("code") or "")) == code), None)
        if not entry:
            send_json(self, {"ok": False, "error": "invalid", "message": "Kodi si sahihi."}, 404)
            return
        stamp = now_ms()
        if stamp >= code_expiry(entry):
            send_json(self, {"ok": False, "error": "expired", "message": "Kodi ime-expire. Omba nyingine kwa admin."}, 410)
            return
        game = next((item for item in catalog_items() if str(item.get("id") or "") == str(entry.get("gameId") or "")), None)
        link = str((game or {}).get("link") or "").strip()
        if not link:
            send_json(self, {"ok": False, "error": "missing_link", "message": "Hakuna download link kwa hii game. Admin aweke link kwanza."}, 409)
            return
        if not entry.get("unlockedAt"):
            entry["used"] = True
            entry["unlockedAt"] = stamp
            entry["expiresAt"] = stamp + CODE_TTL_MS
            save_codes(codes)
        send_json(
            self,
            {
                "ok": True,
                "link": link,
                "gameName": entry.get("gameName") or (game or {}).get("name") or "",
                "expiresAt": code_expiry(entry),
            },
        )

    def save_catalog(self) -> None:
        if not check_admin(self):
            send_json(self, {"ok": False, "error": "Admin code si sahihi au si localhost."}, 403)
            return
        try:
            data = json.loads(read_body(self).decode("utf-8"))
        except Exception:
            send_json(self, {"ok": False, "error": "JSON si sahihi."}, 400)
            return
        if not isinstance(data, dict) or not isinstance(data.get("games"), list):
            send_json(self, {"ok": False, "error": "Catalog haijakamilika."}, 400)
            return
        CATALOG.parent.mkdir(parents=True, exist_ok=True)
        CATALOG.write_text(json.dumps(data, indent=2, ensure_ascii=False) + "\n", encoding="utf-8")
        send_json(self, {"ok": True, "disk": True, "file": "data/catalog.json"})

    def save_cover(self) -> None:
        if not check_admin(self):
            send_json(self, {"ok": False, "error": "Admin code si sahihi au si localhost."}, 403)
            return
        try:
            payload = json.loads(read_body(self).decode("utf-8"))
            raw_name = str(payload.get("name") or "cover.jpg")
            data_url = str(payload.get("data") or "")
            match = re.match(r"data:image/(jpeg|jpg|png|webp);base64,(.+)$", data_url, re.I | re.S)
            if not match:
                raise ValueError("cover")
            ext = match.group(1).lower().replace("jpeg", "jpg")
            blob = base64.b64decode(match.group(2))
            if not blob or len(blob) > 5 * 1024 * 1024:
                raise ValueError("size")
        except Exception:
            send_json(self, {"ok": False, "error": "Cover haijasomeka."}, 400)
            return
        COVERS.mkdir(parents=True, exist_ok=True)
        filename = "%s-%s.%s" % (slug(Path(raw_name).stem), int(time.time()), ext)
        dest = COVERS / filename
        dest.write_bytes(blob)
        send_json(self, {"ok": True, "url": "/assets/img/games/" + filename})


def now_ms() -> int:
    return int(time.time() * 1000)


def read_json_file(path: Path, fallback: dict) -> dict:
    try:
        data = json.loads(path.read_text(encoding="utf-8"))
        return data if isinstance(data, dict) else fallback
    except Exception:
        return fallback


def write_json_file(path: Path, payload: dict) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(json.dumps(payload, indent=2, ensure_ascii=False) + "\n", encoding="utf-8")


def catalog_items() -> list:
    data = read_json_file(CATALOG, {"games": []})
    items = list(data.get("games") or []) + list(data.get("apps") or [])
    if data.get("tips"):
        items.append(data["tips"])
    return items


def load_codes() -> list:
    data = read_json_file(CODES, {"codes": []})
    codes = data.get("codes")
    return codes if isinstance(codes, list) else []


def save_codes(codes: list) -> None:
    write_json_file(CODES, {"codes": codes})


def code_expiry(entry: dict) -> int:
    unlocked = entry.get("unlockedAt")
    if unlocked:
        return int(unlocked) + CODE_TTL_MS
    return int(entry.get("createdAt") or 0) + CODE_TTL_MS


def normalize_code(raw: str) -> str:
    return re.sub(r"\s+", "", str(raw or "")).upper()


def make_code(existing: list) -> str:
    used = {normalize_code(str(item.get("code") or "")) for item in existing}
    alphabet = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789"
    for _ in range(40):
        token = "MILLZ-" + "".join(secrets.choice(alphabet) for _ in range(6))
        if token not in used:
            return token
    return "MILLZ-" + str(now_ms())[-6:]


def pick_port(start: int) -> int:
    for port in range(start, start + 12):
        with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as sock:
            sock.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
            try:
                sock.bind(("127.0.0.1", port))
            except OSError:
                continue
            return port
    raise SystemExit("Hakuna port wazi kutoka %s." % start)


def main() -> None:
    port = pick_port(PREFERRED_PORT)
    server = ThreadingHTTPServer(("127.0.0.1", port), Handler)
    print("", flush=True)
    print("MILLZ GAMES — local admin (bila database)", flush=True)
    print("Store:  http://127.0.0.1:%s/" % port, flush=True)
    print("Admin:  http://127.0.0.1:%s/admin.html" % port, flush=True)
    print("Saves:  data/catalog.json  +  data/codes.json  +  assets/img/games/", flush=True)
    print("Ctrl+C kusimamisha.", flush=True)
    print("", flush=True)
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        print("\nImesimamishwa.")


if __name__ == "__main__":
    main()
