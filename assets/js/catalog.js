(function (global) {
    var STORAGE_KEY = "millz.catalog.v5";
    var CODES_KEY = "millz.codes.v1";
    var ADMIN_KEY = "millz.admin.ok";
    var CATALOG_URL = "/data/catalog.json";
    var CODE_TTL_MS = 4 * 60 * 60 * 1000;

    var MILLZ_WA = "255683179360";
    var MILLZ_WA_DISPLAY = "0683179360";
    var MILLZ_HALOPESA = "0627041240";
    var ADMIN_PASSWORD = "MILLZ005";

    function esc(value) {
        return String(value == null ? "" : value).replace(/[&<>"']/g, function (char) {
            return ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[char];
        });
    }

    function toNumber(price) {
        if (price === null || price === undefined || price === "") return null;
        var n = Number(price);
        return isFinite(n) && n > 0 ? n : null;
    }

    function isFree(item) {
        if (!item || typeof item !== "object") return false;
        if (item.paid === false) return true;
        if (String(item.tier || "").toLowerCase() === "free") return true;
        return false;
    }

    function formatPrice(price) {
        var n = toNumber(price);
        if (n === null) return "Bei: WhatsApp";
        return "TSh " + Math.round(n).toLocaleString("en-US");
    }

    function formatItemPrice(item) {
        if (isFree(item)) return "Free";
        return formatPrice(item && item.price);
    }

    function buyUrl(name, price, kind, item) {
        var label = kind === "tip" ? "eFootball tips" : (kind || "game");
        var free = isFree(item);
        var priceText = free ? "Free" : formatPrice(price).replace(/^Bei:\s*/, "");
        var verb = free ? "Nataka game ya bure" : "Nataka kununua";
        var msg = "Hujambo MILLZ GAMES\n" + verb + " " + label + ": " + name + "\nBei: " + priceText + "\nNitalipa HaloPesa " + MILLZ_HALOPESA + ".\nTafadhali thibitisha malipo.";
        if (free) {
            msg = "Hujambo MILLZ GAMES\n" + verb + ": " + name + "\nTafadhali nipe link.";
        }
        return "https://wa.me/" + MILLZ_WA + "?text=" + encodeURIComponent(msg);
    }

    function waLink() {
        return "https://wa.me/" + MILLZ_WA;
    }

    function publishedItems(list) {
        return (list || []).filter(function (item) {
            return item && item.published !== false;
        });
    }

    function clone(data) {
        return JSON.parse(JSON.stringify(data));
    }

    function mergeCatalog(seed, local) {
        if (!local || !Array.isArray(local.games)) return seed;
        var map = {};
        (seed.games || []).forEach(function (game) {
            map[game.id] = game;
        });
        (local.games || []).forEach(function (game) {
            if (game && game.id) map[game.id] = game;
        });
        var games = Object.keys(map).map(function (id) { return map[id]; });
        var featured = games.filter(function (g) { return g.featured; });
        var rest = games.filter(function (g) { return !g.featured; });
        return {
            version: Math.max(seed.version || 1, local.version || 1),
            games: featured.concat(rest),
            apps: Array.isArray(local.apps) ? local.apps : (seed.apps || []),
            tips: local.tips || seed.tips || null
        };
    }

    function emptyCatalog() {
        return { version: 1, games: [], apps: [], tips: null };
    }

    function pingLocal() {
        var ctrl = typeof AbortController !== "undefined" ? new AbortController() : null;
        var timer = ctrl ? setTimeout(function () { ctrl.abort(); }, 1200) : null;
        return fetch("/api/status", { cache: "no-store", signal: ctrl ? ctrl.signal : undefined })
            .then(function (res) { return res.ok ? res.json() : { local: false }; })
            .then(function (data) { return !!(data && data.local); })
            .catch(function () { return false; })
            .then(function (local) {
                if (timer) clearTimeout(timer);
                return local;
            });
    }

    function adminHeaders(extra) {
        var headers = { "X-Admin-Key": ADMIN_PASSWORD };
        if (extra) {
            Object.keys(extra).forEach(function (key) { headers[key] = extra[key]; });
        }
        return headers;
    }

    function loadCatalog() {
        return pingLocal().then(function (local) {
            return fetch(CATALOG_URL, { cache: "no-store" })
                .then(function (res) { return res.ok ? res.json() : emptyCatalog(); })
                .catch(function () { return emptyCatalog(); })
                .then(function (seed) {
                    if (local) return seed;
                    var stored = null;
                    try {
                        stored = JSON.parse(localStorage.getItem(STORAGE_KEY) || "null");
                    } catch (e) {
                        stored = null;
                    }
                    return mergeCatalog(seed, stored);
                });
        });
    }

    function saveCatalog(data) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        return fetch("/api/catalog", {
            method: "POST",
            headers: adminHeaders({ "Content-Type": "application/json" }),
            body: JSON.stringify(data)
        }).then(function (res) {
            if (!res.ok) throw new Error("disk");
            return res.json();
        }).then(function (info) {
            return { disk: !!(info && info.disk), data: data };
        }).catch(function () {
            return { disk: false, data: data };
        });
    }

    function uploadCover(dataUrl, name) {
        return fetch("/api/cover", {
            method: "POST",
            headers: adminHeaders({ "Content-Type": "application/json" }),
            body: JSON.stringify({ name: name || "cover.jpg", data: dataUrl })
        }).then(function (res) { return res.ok ? res.json() : null; })
            .then(function (info) { return info && info.url ? info.url : ""; })
            .catch(function () { return ""; });
    }

    function uid(prefix) {
        return (prefix || "item") + "-" + Date.now().toString(36) + "-" + Math.random().toString(36).slice(2, 6);
    }

    function catalogItems(data) {
        var list = ((data && data.games) || []).concat((data && data.apps) || []);
        if (data && data.tips) list.push(data.tips);
        return list;
    }

    function normalizeCode(raw) {
        return String(raw || "").trim().toUpperCase().replace(/\s+/g, "");
    }

    function generateAccessCode(existing) {
        var used = {};
        (existing || []).forEach(function (entry) {
            used[normalizeCode(entry && entry.code)] = true;
        });
        var chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
        var i;
        for (i = 0; i < 40; i++) {
            var token = "MILLZ-";
            var j;
            for (j = 0; j < 6; j++) token += chars.charAt(Math.floor(Math.random() * chars.length));
            if (!used[token]) return token;
        }
        return "MILLZ-" + Date.now().toString(36).toUpperCase().slice(-6);
    }

    function codeExpiry(entry) {
        if (!entry) return 0;
        if (entry.unlockedAt) return Number(entry.unlockedAt) + CODE_TTL_MS;
        return Number(entry.createdAt || 0) + CODE_TTL_MS;
    }

    function readLocalCodes() {
        try {
            var data = JSON.parse(localStorage.getItem(CODES_KEY) || "null");
            if (data && Array.isArray(data.codes)) return data.codes;
            if (Array.isArray(data)) return data;
        } catch (e) {
            return [];
        }
        return [];
    }

    function writeLocalCodes(codes) {
        localStorage.setItem(CODES_KEY, JSON.stringify({ codes: codes }));
    }

    function mergeCodes(seed, local) {
        var map = {};
        (seed || []).forEach(function (entry) {
            if (entry && entry.id) map[entry.id] = entry;
        });
        (local || []).forEach(function (entry) {
            if (entry && entry.id) map[entry.id] = entry;
        });
        return Object.keys(map).map(function (id) { return map[id]; }).sort(function (a, b) {
            return (b.createdAt || 0) - (a.createdAt || 0);
        });
    }

    function fetchSeedCodes() {
        return fetch("/data/codes.json", { cache: "no-store" })
            .then(function (res) { return res.ok ? res.json() : { codes: [] }; })
            .catch(function () { return { codes: [] }; })
            .then(function (data) { return Array.isArray(data.codes) ? data.codes : []; });
    }

    function loadCodes() {
        return pingLocal().then(function (local) {
            if (local) {
                return fetch("/api/codes", { cache: "no-store", headers: adminHeaders() })
                    .then(function (res) { return res.ok ? res.json() : { codes: [] }; })
                    .then(function (data) { return Array.isArray(data.codes) ? data.codes : []; })
                    .catch(function () { return fetchSeedCodes(); });
            }
            return fetchSeedCodes().then(function (seed) {
                return mergeCodes(seed, readLocalCodes());
            });
        });
    }

    function applyRedeem(codes, code, catalog, persistLocal) {
        var now = Date.now();
        var entry = (codes || []).find(function (item) {
            return normalizeCode(item.code) === code;
        });
        if (!entry) {
            return { ok: false, error: "invalid", message: "Kodi si sahihi." };
        }
        if (now >= codeExpiry(entry)) {
            return { ok: false, error: "expired", message: "Kodi ime-expire. Omba nyingine kwa admin." };
        }
        var game = catalogItems(catalog).find(function (item) { return item.id === entry.gameId; });
        var link = game && game.link ? String(game.link).trim() : "";
        if (!link) {
            return { ok: false, error: "missing_link", message: "Hakuna download link kwa hii game. Admin aweke link kwanza." };
        }
        if (!entry.unlockedAt) {
            entry.used = true;
            entry.unlockedAt = now;
            entry.expiresAt = now + CODE_TTL_MS;
            if (persistLocal) writeLocalCodes(codes);
        }
        return {
            ok: true,
            link: link,
            gameName: entry.gameName || (game && game.name) || "",
            expiresAt: codeExpiry(entry)
        };
    }

    function issueCode(gameId) {
        return fetch("/api/codes", {
            method: "POST",
            headers: adminHeaders({ "Content-Type": "application/json" }),
            body: JSON.stringify({ gameId: gameId })
        }).then(function (res) {
            return res.json().then(function (data) {
                if (res.ok && data && data.code) return data;
                throw new Error("api");
            });
        }).catch(function () {
            return loadCatalog().then(function (catalog) {
                var game = catalogItems(catalog).find(function (item) { return item.id === gameId; });
                if (!game) return { ok: false, error: "missing_game", message: "Game haijapatikana." };
                return fetchSeedCodes().then(function (seed) {
                    var codes = mergeCodes(seed, readLocalCodes());
                    var now = Date.now();
                    var entry = {
                        id: uid("code"),
                        code: generateAccessCode(codes),
                        gameId: game.id,
                        gameName: game.name,
                        createdAt: now,
                        expiresAt: now + CODE_TTL_MS,
                        used: false,
                        unlockedAt: null
                    };
                    codes.unshift(entry);
                    writeLocalCodes(codes);
                    return { ok: true, disk: false, code: entry.code, entry: entry };
                });
            });
        });
    }

    function redeemCode(raw) {
        var code = normalizeCode(raw);
        if (!code) {
            return Promise.resolve({ ok: false, error: "invalid", message: "Weka kodi uliyopewa na admin baada ya malipo." });
        }
        return fetch("/api/codes/redeem", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ code: code })
        }).then(function (res) {
            return res.json().then(function (data) {
                if (data && (data.ok === true || data.error)) return data;
                throw new Error("api");
            });
        }).catch(function () {
            return loadCatalog().then(function (catalog) {
                return fetchSeedCodes().then(function (seed) {
                    var codes = mergeCodes(seed, readLocalCodes());
                    return applyRedeem(codes, code, catalog, true);
                });
            });
        });
    }

    function isAdmin() {
        try {
            return sessionStorage.getItem(ADMIN_KEY) === "1";
        } catch (e) {
            return false;
        }
    }

    function loginAdmin(password) {
        if (password === ADMIN_PASSWORD) {
            sessionStorage.setItem(ADMIN_KEY, "1");
            return true;
        }
        return false;
    }

    function logoutAdmin() {
        sessionStorage.removeItem(ADMIN_KEY);
    }

    function fileToCover(file) {
        return new Promise(function (resolve, reject) {
            if (!file) return resolve("");
            var img = new Image();
            var url = URL.createObjectURL(file);
            img.onload = function () {
                var max = 900;
                var w = img.width;
                var h = img.height;
                if (w > max) {
                    h = Math.round(h * max / w);
                    w = max;
                }
                var canvas = document.createElement("canvas");
                canvas.width = w;
                canvas.height = h;
                canvas.getContext("2d").drawImage(img, 0, 0, w, h);
                URL.revokeObjectURL(url);
                resolve(canvas.toDataURL("image/jpeg", 0.78));
            };
            img.onerror = function () {
                URL.revokeObjectURL(url);
                reject(new Error("Cover haikusomeka"));
            };
            img.src = url;
        });
    }

    global.MILLZ = {
        WA: MILLZ_WA,
        WA_DISPLAY: MILLZ_WA_DISPLAY,
        HALOPESA: MILLZ_HALOPESA,
        esc: esc,
        formatPrice: formatPrice,
        formatItemPrice: formatItemPrice,
        isFree: isFree,
        buyUrl: buyUrl,
        waLink: waLink,
        publishedItems: publishedItems,
        loadCatalog: loadCatalog,
        saveCatalog: saveCatalog,
        uploadCover: uploadCover,
        pingLocal: pingLocal,
        uid: uid,
        isAdmin: isAdmin,
        loginAdmin: loginAdmin,
        logoutAdmin: logoutAdmin,
        fileToCover: fileToCover,
        toNumber: toNumber,
        CODE_TTL_MS: CODE_TTL_MS,
        loadCodes: loadCodes,
        issueCode: issueCode,
        redeemCode: redeemCode,
        codeExpiry: codeExpiry,
        normalizeCode: normalizeCode
    };
})(window);
