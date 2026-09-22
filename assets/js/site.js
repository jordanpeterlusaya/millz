const menuBtn = document.getElementById("menuBtn");
const navLinks = document.getElementById("navLinks");

if (menuBtn && navLinks) {
    menuBtn.addEventListener("click", function () {
        const open = navLinks.classList.toggle("show");
        menuBtn.setAttribute("aria-expanded", open ? "true" : "false");
        document.body.classList.toggle("nav-open", open);
        const icon = menuBtn.querySelector("i");
        if (icon) icon.className = open ? "fa-solid fa-xmark" : "fa-solid fa-bars";
    });
    navLinks.querySelectorAll("a").forEach(function (link) {
        link.addEventListener("click", function () {
            navLinks.classList.remove("show");
            document.body.classList.remove("nav-open");
            menuBtn.setAttribute("aria-expanded", "false");
            const icon = menuBtn.querySelector("i");
            if (icon) icon.className = "fa-solid fa-bars";
        });
    });
}

let rotatorTimer = null;
let heroTimer = null;

function initHeroSlides() {
    const slides = document.querySelectorAll(".hero-slide");
    if (heroTimer) clearInterval(heroTimer);
    if (slides.length < 2) return;
    let currentSlide = 0;
    heroTimer = setInterval(function () {
        slides[currentSlide].classList.remove("active");
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add("active");
    }, 4500);
}

function initCoverRotator() {
    const items = Array.from(document.querySelectorAll(".cover-slide"));
    if (rotatorTimer) clearInterval(rotatorTimer);
    if (!items.length) return;
    let index = 0;

    function render() {
        const n = items.length;
        items.forEach(function (el, i) {
            el.classList.remove("active", "prev", "next");
            if (i === index) el.classList.add("active");
            else if (i === (index - 1 + n) % n) el.classList.add("prev");
            else if (i === (index + 1) % n) el.classList.add("next");
        });
        const caption = document.getElementById("rotatorCaption");
        if (caption) {
            const active = items[index];
            caption.textContent = (active.dataset.name || "") + "  •  " + (active.dataset.price || "");
        }
    }

    render();
    rotatorTimer = setInterval(function () {
        index = (index + 1) % items.length;
        render();
    }, 3200);

    document.querySelectorAll("[data-rotator-nav]").forEach(function (btn) {
        btn.onclick = function () {
            index = this.dataset.rotatorNav === "next"
                ? (index + 1) % items.length
                : (index - 1 + items.length) % items.length;
            render();
        };
    });
}

function filterCards(selector, value, attr) {
    document.querySelectorAll(selector).forEach(function (card) {
        const hay = (card.getAttribute(attr) || "").toLowerCase();
        card.style.display = (!value || value === "all" || hay.indexOf(value) !== -1) ? "" : "none";
    });
}

const searchInput = document.getElementById("searchInput");
if (searchInput) {
    searchInput.addEventListener("input", function () {
        filterCards(".searchable", this.value.toLowerCase().trim(), "data-name");
    });
}

const searchBtn = document.getElementById("searchBtn");
if (searchBtn) {
    searchBtn.addEventListener("click", function () {
        const games = document.getElementById("games");
        if (games) games.scrollIntoView({ behavior: "smooth" });
    });
}

document.querySelectorAll("#categories .category[data-filter]").forEach(function (button) {
    button.addEventListener("click", function (e) {
        e.preventDefault();
        document.querySelectorAll("#categories .category[data-filter]").forEach(function (btn) {
            btn.classList.remove("active");
        });
        this.classList.add("active");
        const filter = (this.getAttribute("data-filter") || "all").toLowerCase();
        document.querySelectorAll(".game-card").forEach(function (card) {
            const category = (card.getAttribute("data-category") || "").toLowerCase();
            const tier = (card.getAttribute("data-tier") || "paid").toLowerCase();
            let show = true;
            if (filter === "paid" || filter === "free") show = tier === filter;
            else if (filter !== "all") show = category === filter;
            card.style.display = show ? "" : "none";
        });
        const paidSec = document.getElementById("paid");
        const freeSec = document.getElementById("free");
        if (paidSec) paidSec.style.display = filter === "free" ? "none" : "";
        if (freeSec) freeSec.style.display = filter === "paid" ? "none" : "";
        const games = document.getElementById(filter === "free" ? "free" : "games");
        if (games) games.scrollIntoView({ behavior: "smooth" });
    });
});

document.querySelectorAll("#apps .category[data-app-filter]").forEach(function (button) {
    button.addEventListener("click", function (e) {
        e.preventDefault();
        document.querySelectorAll("#apps .category[data-app-filter]").forEach(function (btn) {
            btn.classList.remove("active");
        });
        this.classList.add("active");
        const filter = (this.getAttribute("data-app-filter") || "all").toLowerCase();
        document.querySelectorAll(".app-card").forEach(function (card) {
            const category = (card.getAttribute("data-category") || "").toLowerCase();
            card.style.display = (filter === "all" || category === filter) ? "" : "none";
        });
    });
});

const buyModal = document.getElementById("buyModal");
const buyCover = document.getElementById("buyCover");
const buyName = document.getElementById("buyName");
const buyPrice = document.getElementById("buyPrice");
const buyWhatsApp = document.getElementById("buyWhatsApp");

function openBuyModal(name, price, img, wa) {
    if (!buyModal) return;
    if (buyName) buyName.textContent = name || "MILLZ DROP";
    if (buyPrice) buyPrice.textContent = price || MILLZ.formatPrice(null);
    if (buyCover) {
        if (img) {
            buyCover.innerHTML = '<img src="' + img + '" alt="">';
        } else {
            buyCover.innerHTML = '<div class="no-image">NO COVER</div>';
        }
    }
    if (buyWhatsApp) buyWhatsApp.href = wa || MILLZ.waLink();
    buyModal.classList.add("show");
}

function closeBuyModal() {
    if (buyModal) buyModal.classList.remove("show");
}

document.addEventListener("click", function (e) {
    const btn = e.target.closest("[data-buy]");
    if (btn) {
        openBuyModal(btn.dataset.name, btn.dataset.price, btn.dataset.img, btn.dataset.wa);
        return;
    }
    if (e.target.closest("[data-close-buy]")) closeBuyModal();
});

if (buyModal) {
    buyModal.addEventListener("click", function (e) {
        if (e.target === buyModal) closeBuyModal();
    });
}

function buyAttrs(item) {
    const priceLabel = MILLZ.formatItemPrice(item);
    return 'data-buy data-name="' + MILLZ.esc(item.name) + '" data-price="' + MILLZ.esc(priceLabel) + '" data-img="' + MILLZ.esc(item.cover || "") + '" data-kind="' + MILLZ.esc(item.kind || "game") + '" data-wa="' + MILLZ.esc(MILLZ.buyUrl(item.name, item.price, item.kind, item)) + '"';
}

function platformMeta(platform) {
    const p = platform || "Android / Windows";
    const bits = [];
    if (/android/i.test(p)) bits.push('<i class="fa-brands fa-android"></i> Android');
    if (/windows/i.test(p)) bits.push('<i class="fa-brands fa-windows"></i> Windows');
    if (!bits.length) return '<div class="meta"><i class="fa-solid fa-desktop"></i> ' + MILLZ.esc(p) + "</div>";
    return '<div class="meta">' + bits.join(" · ") + "</div>";
}

function productCard(item) {
    const kind = item.kind === "app" ? "app" : "game";
    const free = MILLZ.isFree(item);
    const cover = item.cover
        ? '<img src="' + MILLZ.esc(item.cover) + '" alt="' + MILLZ.esc(item.name) + '">'
        : '<div class="no-image">NO COVER</div>';
    const priceLabel = MILLZ.formatItemPrice(item);
    const cta = free && item.link
        ? '<a href="' + MILLZ.esc(item.link) + '" class="product-btn buy" target="_blank" rel="noopener">Get free</a>'
        : '<button type="button" class="product-btn buy" ' + buyAttrs(item) + ">" + (free ? "Get free" : "Buy now") + "</button>";
    return '<article class="product-card searchable ' + kind + '-card" data-name="' + MILLZ.esc((item.name || "").toLowerCase()) + '" data-category="' + MILLZ.esc(item.tag || "") + '" data-tier="' + (free ? "free" : "paid") + '">' +
        '<div class="product-image">' + cover + '<div class="price-tag">' + MILLZ.esc(priceLabel) + "</div></div>" +
        '<div class="product-info"><h3>' + MILLZ.esc(item.name) + "</h3>" +
        '<div class="price">' + MILLZ.esc(priceLabel) + "</div>" +
        '<div class="meta"><i class="fa-solid fa-layer-group"></i> ' + MILLZ.esc(item.tag || kind) + "</div>" +
        platformMeta(item.platform) +
        '<div class="product-actions">' +
        cta +
        '<a href="/#access" class="product-btn">Get access</a>' +
        "</div></div></article>";
}

function renderStore(catalog) {
    const games = MILLZ.publishedItems(catalog.games);
    const paidGames = games.filter(function (g) { return !MILLZ.isFree(g); });
    const freeGames = games.filter(function (g) { return MILLZ.isFree(g); });
    const apps = MILLZ.publishedItems(catalog.apps);
    const featured = paidGames.find(function (g) { return g.featured; }) || games[0];
    const wides = games.map(function (g) { return g.wide || g.cover; }).filter(Boolean).slice(0, 5);

    const heroBg = document.getElementById("heroBg");
    if (heroBg) {
        heroBg.innerHTML = (wides.length ? wides : ["/assets/img/games/gta-vi-wide.jpg"]).map(function (src, i) {
            return '<div class="hero-slide' + (i === 0 ? " active" : "") + '" style="background-image:url(\'' + MILLZ.esc(src) + "');\"></div>";
        }).join("");
    }

    const heroFeature = document.getElementById("heroFeature");
    if (heroFeature && featured) {
        heroFeature.setAttribute("data-name", featured.name);
        heroFeature.setAttribute("data-price", MILLZ.formatItemPrice(featured));
        heroFeature.setAttribute("data-img", featured.cover || "");
        heroFeature.setAttribute("data-kind", "game");
        heroFeature.setAttribute("data-wa", MILLZ.buyUrl(featured.name, featured.price, "game", featured));
        heroFeature.setAttribute("data-buy", "");
        heroFeature.innerHTML =
            '<img src="' + MILLZ.esc(featured.wide || featured.cover || "") + '" alt="' + MILLZ.esc(featured.name) + '">' +
            '<div class="hero-feature-copy"><small>Featured</small><h2>' + MILLZ.esc(featured.name) + "</h2>" +
            '<p>' + MILLZ.esc(featured.tag || "Action") + " · " + MILLZ.esc(MILLZ.formatItemPrice(featured)) + " · " + MILLZ.esc(featured.platform || "Android / Windows") + "</p>" +
            '<span class="btn btn-primary">Buy now</span></div>';
    }

    const gameCount = document.getElementById("gameCount");
    if (gameCount) gameCount.textContent = String(games.length);

    const rotator = document.getElementById("coverRotator");
    if (rotator) {
        rotator.querySelectorAll(".cover-slide").forEach(function (el) { el.remove(); });
        const nextBtn = rotator.querySelector(".rotator-nav.next");
        games.forEach(function (game, i) {
            const article = document.createElement("article");
            article.className = "cover-slide" + (i === 0 ? " active" : i === 1 ? " next" : i === games.length - 1 ? " prev" : "");
            article.setAttribute("data-buy", "");
            article.dataset.name = game.name;
            article.dataset.price = MILLZ.formatItemPrice(game);
            article.dataset.img = game.cover || "";
            article.dataset.kind = "game";
            article.dataset.wa = MILLZ.buyUrl(game.name, game.price, "game", game);
            article.dataset.tag = game.tag || "Game";
            article.innerHTML = '<img src="' + MILLZ.esc(game.cover || "") + '" alt="' + MILLZ.esc(game.name) + '"><div class="price-tag">' + MILLZ.esc(MILLZ.formatItemPrice(game)) + "</div>";
            rotator.insertBefore(article, nextBtn);
        });
    }

    const paidGrid = document.getElementById("paidGrid") || document.getElementById("gameGrid");
    if (paidGrid) {
        paidGrid.innerHTML = paidGames.length
            ? paidGames.map(productCard).join("")
            : '<div class="empty">Paid games zitaonekana hapa baada ya admin kuziongeza.</div>';
    }

    const freeGrid = document.getElementById("freeGrid");
    if (freeGrid) {
        freeGrid.innerHTML = freeGames.length
            ? freeGames.map(productCard).join("")
            : '<div class="empty">Free games zitaonekana hapa. Admin anaweza kuongeza gemu za bure kwenye /admin.html</div>';
    }

    const appGrid = document.getElementById("appGrid");
    if (appGrid) {
        appGrid.innerHTML = apps.length
            ? apps.map(productCard).join("")
            : '<div class="empty">Apps published in admin will appear here with Buy now and Get access.</div>';
    }

    const tipsBuy = document.getElementById("tipsBuy");
    if (tipsBuy && catalog.tips) {
        const tip = catalog.tips;
        tipsBuy.setAttribute("data-buy", "");
        tipsBuy.dataset.name = tip.name;
        tipsBuy.dataset.price = MILLZ.formatItemPrice(tip);
        tipsBuy.dataset.img = tip.cover || "";
        tipsBuy.dataset.kind = "tip";
        tipsBuy.dataset.wa = MILLZ.buyUrl(tip.name, tip.price, "tip", tip);
        const tipsPrice = document.getElementById("tipsPrice");
        if (tipsPrice) tipsPrice.textContent = MILLZ.formatItemPrice(tip);
    }

    initHeroSlides();
    initCoverRotator();
}

if (window.MILLZ) {
    MILLZ.loadCatalog().then(renderStore);
}
