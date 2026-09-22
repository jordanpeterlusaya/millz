(function () {
    var catalog = { version: 1, games: [], apps: [], tips: null };
    var editingCover = "";

    var loginView = document.getElementById("loginView");
    var dashView = document.getElementById("dashView");
    var loginForm = document.getElementById("loginForm");
    var loginError = document.getElementById("loginError");
    var itemForm = document.getElementById("itemForm");
    var formOk = document.getElementById("formOk");
    var coverPreview = document.getElementById("coverPreview");

    function showDash(data) {
        loginView.hidden = true;
        dashView.hidden = false;
        if (data) {
            catalog = data;
            renderList();
            renderNotices();
            return;
        }
        MILLZ.loadCatalog().then(function (loaded) {
            catalog = loaded;
            renderList();
            renderNotices();
        });
    }

    function showLogin() {
        loginView.hidden = false;
        dashView.hidden = true;
    }

    window.millzAdminReady = function (data) {
        showDash(data);
    };

    if (window.MILLZ && MILLZ.isAdmin()) showDash();
    else showLogin();

    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            e.preventDefault();
            if (!window.MILLZ) return;
            if (MILLZ.loginAdmin(document.getElementById("adminPass").value)) {
                if (loginError) loginError.hidden = true;
                showDash();
            } else if (loginError) {
                loginError.hidden = false;
                loginError.textContent = "Password si sahihi. Tumia MILLZ005";
            }
        });
    }

    var loginBtn = document.getElementById("loginBtn");
    if (loginBtn) {
        loginBtn.addEventListener("click", function (e) {
            e.preventDefault();
            if (loginForm && typeof loginForm.requestSubmit === "function") loginForm.requestSubmit();
            else if (loginForm) loginForm.dispatchEvent(new Event("submit", { cancelable: true }));
        });
    }

    var logoutBtn = document.getElementById("logoutBtn");
    if (logoutBtn) {
        logoutBtn.addEventListener("click", function () {
            if (window.MILLZ) MILLZ.logoutAdmin();
            showLogin();
        });
    }

    function allItems() {
        var list = (catalog.games || []).concat(catalog.apps || []);
        if (catalog.tips) list.push(catalog.tips);
        return list;
    }

    function renderList() {
        var host = document.getElementById("adminList");
        var items = allItems();
        if (!items.length) {
            host.innerHTML = '<p class="admin-lead">Hakuna kitu bado. Ongeza game hapo juu.</p>';
            return;
        }
        host.innerHTML = items.map(function (item) {
            var img = item.cover
                ? '<img src="' + MILLZ.esc(item.cover) + '" alt="">'
                : '<div class="no-image">NO COVER</div>';
            return '<article class="admin-item">' +
                img +
                "<div><strong>" + MILLZ.esc(item.name) + "</strong>" +
                "<span>" + MILLZ.esc(item.kind || "game") + " · " + (MILLZ.isFree(item) ? "Free" : "Paid") + " · " + MILLZ.esc(MILLZ.formatItemPrice(item)) + " · " + MILLZ.esc(item.platform || "Android / Windows") + "</span>" +
                (item.link ? '<a href="' + MILLZ.esc(item.link) + '" target="_blank" rel="noopener">Link</a>' : "<em>Hakuna link</em>") +
                "</div>" +
                '<div class="admin-item-actions">' +
                '<button type="button" data-edit="' + MILLZ.esc(item.id) + '">Edit</button>' +
                '<button type="button" class="danger" data-del="' + MILLZ.esc(item.id) + '">Delete</button>' +
                "</div></article>";
        }).join("");
    }

    document.getElementById("adminList").addEventListener("click", function (e) {
        var editId = e.target.getAttribute("data-edit");
        var delId = e.target.getAttribute("data-del");
        if (editId) fillForm(findItem(editId));
        if (delId && confirm("Futa hii game?")) {
            removeItem(delId);
            persist().then(function (info) {
                renderList();
                resetForm();
                showSaved("Game", info);
            });
        }
    });

    function findItem(id) {
        return allItems().find(function (item) { return item.id === id; }) || null;
    }

    function removeItem(id) {
        catalog.games = (catalog.games || []).filter(function (g) { return g.id !== id; });
        catalog.apps = (catalog.apps || []).filter(function (g) { return g.id !== id; });
        if (catalog.tips && catalog.tips.id === id) catalog.tips = null;
    }

    function fillForm(item) {
        if (!item) return;
        document.getElementById("formTitle").textContent = "Hariri " + item.name;
        document.getElementById("itemId").value = item.id;
        document.getElementById("itemKind").value = item.kind || "game";
        document.getElementById("itemTag").value = item.tag || "";
        document.getElementById("itemName").value = item.name || "";
        document.getElementById("itemPrice").value = MILLZ.toNumber(item.price) || "";
        document.getElementById("itemPlatform").value = item.platform || "Android / Windows";
        document.getElementById("itemPaid").value = MILLZ.isFree(item) ? "free" : "paid";
        document.getElementById("itemLink").value = item.link || "";
        document.getElementById("itemCoverUrl").value = (item.cover && item.cover.indexOf("data:") !== 0) ? item.cover : "";
        document.getElementById("itemFeatured").checked = !!item.featured;
        document.getElementById("itemPublished").checked = item.published !== false;
        editingCover = item.cover || "";
        if (editingCover) {
            coverPreview.src = editingCover;
            coverPreview.hidden = false;
        }
        window.scrollTo({ top: 0, behavior: "smooth" });
    }

    function resetForm() {
        itemForm.reset();
        document.getElementById("itemId").value = "";
        document.getElementById("itemPublished").checked = true;
        document.getElementById("itemPlatform").value = "Android / Windows";
        document.getElementById("itemPaid").value = "paid";
        document.getElementById("formTitle").textContent = "Ongeza game";
        editingCover = "";
        coverPreview.hidden = true;
        coverPreview.removeAttribute("src");
        formOk.hidden = true;
    }

    document.getElementById("resetBtn").addEventListener("click", resetForm);

    document.getElementById("itemCoverFile").addEventListener("change", function () {
        var file = this.files && this.files[0];
        if (!file) return;
        MILLZ.fileToCover(file).then(function (data) {
            coverPreview.src = data;
            coverPreview.hidden = false;
            editingCover = data;
            return MILLZ.uploadCover(data, file.name);
        }).then(function (url) {
            if (url) {
                editingCover = url;
                document.getElementById("itemCoverUrl").value = url;
            }
        });
    });

    itemForm.addEventListener("submit", function (e) {
        e.preventDefault();
        var id = document.getElementById("itemId").value || MILLZ.uid("game");
        var kind = document.getElementById("itemKind").value;
        var coverUrl = document.getElementById("itemCoverUrl").value.trim();
        var item = {
            id: id,
            name: document.getElementById("itemName").value.trim(),
            kind: kind,
            tag: document.getElementById("itemTag").value.trim() || (kind === "app" ? "App" : "Game"),
            platform: document.getElementById("itemPlatform").value.trim() || "Android / Windows",
            price: MILLZ.toNumber(document.getElementById("itemPrice").value),
            paid: document.getElementById("itemPaid").value !== "free",
            cover: editingCover || coverUrl,
            wide: editingCover || coverUrl,
            link: document.getElementById("itemLink").value.trim(),
            featured: document.getElementById("itemFeatured").checked,
            published: document.getElementById("itemPublished").checked
        };
        if (!item.name) return;
        removeItem(id);
        if (kind === "tip") catalog.tips = item;
        else if (kind === "app") catalog.apps.push(item);
        else {
            if (item.featured) {
                catalog.games.forEach(function (g) { g.featured = false; });
            }
            catalog.games.unshift(item);
        }
        persist().then(function (info) {
            renderList();
            var savedName = item.name;
            resetForm();
            showSaved(savedName, info);
        });
    });

    function persist() {
        return MILLZ.saveCatalog(catalog);
    }

    function showSaved(name, info) {
        formOk.hidden = false;
        if (info && info.disk) {
            formOk.textContent = name + " imehifadhiwa kwenye laptop (data/catalog.json). Itaonekana kwenye store.";
        } else {
            formOk.textContent = name + " imehifadhiwa kwenye browser hii. Endesha python3 admin-server.py ili iandikwe kwenye faili.";
        }
    }

    MILLZ.pingLocal().then(function (local) {
        var status = document.getElementById("localStatus");
        if (!status) return;
        status.hidden = false;
        status.textContent = local
            ? "Localhost iko. Save inaandika kwenye data/catalog.json — hakuna database."
            : "Browser mode. Endesha python3 admin-server.py kwenye folder hii ili Save iandike kwenye laptop.";
    });

    function renderNotices() {
        var host = document.getElementById("notifyList");
        if (!host || !window.MILLZ) return;
        MILLZ.listCodes().then(function (list) {
            if (!list.length) {
                host.innerHTML = '<p class="admin-lead">Hakuna kodi bado. Mteja akibonyeza Generate Code, ujumbe utatokea hapa.</p>';
                return;
            }
            host.innerHTML = list.map(function (entry) {
                var expired = MILLZ.isCodeExpired(entry);
                var klass = "notify-item" + (entry.paid ? " paid" : "") + (expired ? " expired" : "");
                var extra = expired ? "Ime-expire." : (entry.paid ? "Malipo yamethibitishwa." : "Subiri screenshot kwenye WhatsApp.");
                var action = entry.paid || expired
                    ? ""
                    : '<button type="button" class="btn btn-outline" data-paid="' + MILLZ.esc(entry.id) + '">Thibitisha malipo</button>';
                return '<article class="' + klass + '"><p>' + MILLZ.esc(MILLZ.codeNotice(entry)) + "</p>" +
                    '<div class="meta">' + MILLZ.esc(extra) + "</div>" + action + "</article>";
            }).join("");
        });
    }

    var notifyHost = document.getElementById("notifyList");
    if (notifyHost) {
        notifyHost.addEventListener("click", function (e) {
            var id = e.target.getAttribute("data-paid");
            if (!id) return;
            MILLZ.markCodePaid(id).then(renderNotices);
        });
    }

    setInterval(function () {
        if (dashView && !dashView.hidden) renderNotices();
    }, 12000);
})();
