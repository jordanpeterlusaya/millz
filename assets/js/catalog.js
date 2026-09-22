(function (global) {
    var STORAGE_KEY = "millz.catalog.v5";
    var CODES_KEY = "millz.codes.v1";
    var ADMIN_KEY = "millz.admin.ok";
    var CATALOG_URL = "/data/catalog.json";
    var CODE_TTL = 24 * 60 * 60 * 1000;

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

    function readLocalCodes() {
        try {
            var parsed = JSON.parse(localStorage.getItem(CODES_KEY) || "[]");
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            return [];
        }
    }

    function writeLocalCodes(list) {
        localStorage.setItem(CODES_KEY, JSON.stringify(list || []));
    }

    function upsertCode(list, entry) {
        var next = (list || []).filter(function (item) { return item && item.id !== entry.id && item.code !== entry.code; });
        next.unshift(entry);
        return next;
    }

    function randomCode() {
        var chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
        var out = "MILLZ-";
        for (var i = 0; i < 6; i++) out += chars.charAt(Math.floor(Math.random() * chars.length));
        return out;
    }

    function formatExpiry(ms) {
        try {
            return new Date(ms).toLocaleString("en-GB", {
                day: "2-digit",
                month: "short",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit"
            });
        } catch (e) {
            return String(ms || "");
        }
    }

    function isCodeExpired(entry) {
        return !entry || !entry.expiresAt || Date.now() > Number(entry.expiresAt);
    }

    function codeNotice(entry) {
        return "Kuna mteja ametengeneza kodi " + (entry.code || "") + ", ya gemu " + (entry.gameName || "") + ", na ita-expire muda " + formatExpiry(entry.expiresAt) + ".";
    }

    function codeWhatsApp(entry) {
        var msg = "Hujambo MILLZ GAMES\nNimetengeneza kodi: " + entry.code + "\nGemu: " + entry.gameName + "\nIta-expire: " + formatExpiry(entry.expiresAt) + "\nNitalipa HaloPesa " + MILLZ_HALOPESA + ".\nTafadhali thibitisha malipo.";
        return "https://wa.me/" + MILLZ_WA + "?text=" + encodeURIComponent(msg);
    }

    function createAccessCode(game) {
        var now = Date.now();
        var entry = {
            id: uid("code"),
            code: randomCode(),
            gameId: game && game.id ? game.id : "",
            gameName: game && game.name ? game.name : "Game",
            createdAt: now,
            expiresAt: now + CODE_TTL,
            paid: false,
            used: false
        };
        writeLocalCodes(upsertCode(readLocalCodes(), entry));
        return fetch("/api/codes", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(entry)
        }).then(function (res) { return res.ok ? entry : entry; })
            .catch(function () { return entry; });
    }

    function listCodes() {
        var local = readLocalCodes();
        return pingLocal().then(function (ok) {
            if (!ok) return local;
            return fetch("/api/codes", { headers: adminHeaders(), cache: "no-store" })
                .then(function (res) { return res.ok ? res.json() : { codes: [] }; })
                .then(function (data) {
                    var remote = (data && data.codes) || [];
                    var map = {};
                    local.concat(remote).forEach(function (item) {
                        if (!item || !item.id) return;
                        var prev = map[item.id];
                        if (!prev || (item.createdAt || 0) >= (prev.createdAt || 0)) map[item.id] = item;
                    });
                    var merged = Object.keys(map).map(function (id) { return map[id]; });
                    merged.sort(function (a, b) { return (b.createdAt || 0) - (a.createdAt || 0); });
                    writeLocalCodes(merged);
                    return merged;
                })
                .catch(function () { return local; });
        });
    }

    function findCode(raw) {
        var needle = String(raw || "").trim().toUpperCase();
        return listCodes().then(function (list) {
            return list.find(function (item) { return String(item.code || "").toUpperCase() === needle; }) || null;
        });
    }

    function markCodePaid(id) {
        var list = readLocalCodes().map(function (item) {
            if (item && item.id === id) item.paid = true;
            return item;
        });
        writeLocalCodes(list);
        return fetch("/api/codes/paid", {
            method: "POST",
            headers: adminHeaders({ "Content-Type": "application/json" }),
            body: JSON.stringify({ id: id })
        }).then(function () { return true; }).catch(function () { return true; });
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
        createAccessCode: createAccessCode,
        listCodes: listCodes,
        findCode: findCode,
        markCodePaid: markCodePaid,
        isCodeExpired: isCodeExpired,
        formatExpiry: formatExpiry,
        codeNotice: codeNotice,
        codeWhatsApp: codeWhatsApp
    };
})(window);
