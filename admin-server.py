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
import socket
import time
from http.server import SimpleHTTPRequestHandler, ThreadingHTTPServer
from pathlib import Path
from urllib.parse import urlparse

ROOT = Path(__file__).resolve().parent
CATALOG = ROOT / "data" / "catalog.json"
COVERS = ROOT / "assets" / "img" / "games"
ADMIN_CODE = "MILLZ005"
PREFERRED_PORT = 8765


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
        super().do_GET()

    def do_POST(self) -> None:
        path = urlparse(self.path).path
        if path == "/api/catalog":
            self.save_catalog()
            return
        if path == "/api/cover":
            self.save_cover()
            return
        self.send_error(404, "Not found")

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
    print("Saves:  data/catalog.json  +  assets/img/games/", flush=True)
    print("Ctrl+C kusimamisha.", flush=True)
    print("", flush=True)
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        print("\nImesimamishwa.")


if __name__ == "__main__":
    main()
