(function (global) {
    var STORAGE_KEY = "millz.catalog.v1";
    var ADMIN_KEY = "millz.admin.ok";
    var CATALOG_URL = "/data/catalog.json";

    var MILLZ_WA = "255683179360";
    var MILLZ_WA_DISPLAY = "+255 683 179 360";
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

    function formatPrice(price) {
        var n = toNumber(price);
        if (n === null) return "Bei: WhatsApp";
        return "TSh " + Math.round(n).toLocaleString("en-US");
    }

    function buyUrl(name, price, kind) {
        var label = kind === "tip" ? "eFootball tips" : (kind || "game");
        var msg = "Hujambo MILLZ GAMES\nNataka kununua " + label + ": " + name + "\nBei: " + formatPrice(price).replace(/^Bei:\s*/, "") + "\nNitalipa HaloPesa " + MILLZ_HALOPESA + ".\nTafadhali nipe access code.";
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

    function loadCatalog() {
        return fetch(CATALOG_URL, { cache: "no-store" })
            .then(function (res) { return res.ok ? res.json() : emptyCatalog(); })
            .catch(function () { return emptyCatalog(); })
            .then(function (seed) {
                var local = null;
                try {
                    local = JSON.parse(localStorage.getItem(STORAGE_KEY) || "null");
                } catch (e) {
                    local = null;
                }
                return mergeCatalog(seed, local);
            });
    }

    function saveCatalog(data) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        return data;
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
        buyUrl: buyUrl,
        waLink: waLink,
        publishedItems: publishedItems,
        loadCatalog: loadCatalog,
        saveCatalog: saveCatalog,
        uid: uid,
        isAdmin: isAdmin,
        loginAdmin: loginAdmin,
        logoutAdmin: logoutAdmin,
        fileToCover: fileToCover,
        toNumber: toNumber
    };
})(window);
