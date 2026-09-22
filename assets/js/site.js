const menuBtn = document.getElementById("menuBtn");
const navLinks = document.getElementById("navLinks");

if (menuBtn && navLinks) {
    menuBtn.addEventListener("click", function () {
        navLinks.classList.toggle("show");
    });
    navLinks.querySelectorAll("a").forEach(function (link) {
        link.addEventListener("click", function () {
            navLinks.classList.remove("show");
        });
    });
}

const slides = document.querySelectorAll(".hero-slide");
let currentSlide = 0;
if (slides.length > 1) {
    setInterval(function () {
        slides[currentSlide].classList.remove("active");
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add("active");
    }, 4500);
}

function initCoverRotator() {
    const items = Array.from(document.querySelectorAll(".cover-slide"));
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
            caption.textContent = (active.dataset.name || "") + "  •  " + (active.dataset.tag || "Game");
        }
    }

    render();
    setInterval(function () {
        index = (index + 1) % items.length;
        render();
    }, 3200);

    document.querySelectorAll("[data-rotator-nav]").forEach(function (btn) {
        btn.addEventListener("click", function () {
            index = this.dataset.rotatorNav === "next"
                ? (index + 1) % items.length
                : (index - 1 + items.length) % items.length;
            render();
        });
    });
}

initCoverRotator();

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
            card.style.display = (filter === "all" || category === filter) ? "" : "none";
        });
        const games = document.getElementById("games");
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
    if (buyPrice) buyPrice.textContent = price || "Price: Set by Admin";
    if (buyCover) {
        if (img) {
            buyCover.innerHTML = '<img src="' + img + '" alt="">';
        } else {
            buyCover.innerHTML = '<div class="no-image">NO COVER</div>';
        }
    }
    if (buyWhatsApp) buyWhatsApp.href = wa || "https://wa.me/255627041240";
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
