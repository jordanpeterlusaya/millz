<?php
if (!function_exists("millz_price")) {
    function millz_price($price)
    {
        if ($price === null || $price === "" || (float)$price <= 0) {
            return "Bei: WhatsApp";
        }
        return "TSh " . number_format((float)$price, 0);
    }
}

if (!function_exists("millz_cover")) {
    function millz_cover($item)
    {
        if (!empty($item["cover_src"])) {
            return $item["cover_src"];
        }
        if (!empty($item["cover_image"])) {
            return "uploads/covers/" . $item["cover_image"];
        }
        return "";
    }
}

if (!function_exists("millz_buy_url")) {
    function millz_buy_url($name, $price = null, $kind = "game")
    {
        $priceText = millz_price($price);
        $label = $kind === "tip" ? "eFootball tips" : $kind;
        $msg = "Hujambo MILLZ GAMES\nNataka kununua {$label}: {$name}\nBei: {$priceText}\nTafadhali nipe maelezo ya malipo na access code.";
        return "https://wa.me/255683179360?text=" . rawurlencode($msg);
    }
}

if (!function_exists("millz_showcase_games")) {
    function millz_showcase_games()
    {
        return [
            ["name" => "Grand Theft Auto V", "image" => "assets/img/games/gta-v.jpg", "wide" => "assets/img/games/gta-v-wide.jpg", "tag" => "Action"],
            ["name" => "Football 2027", "image" => "assets/img/games/football-2027.jpg", "wide" => "assets/img/games/football-2027-wide.jpg", "tag" => "Sports"],
            ["name" => "EA Sports FC 27", "image" => "assets/img/games/fc-27.jpg", "wide" => "assets/img/games/fc-27-wide.jpg", "tag" => "Sports"],
            ["name" => "Subway Surfers", "image" => "assets/img/games/subway.jpg", "wide" => "assets/img/games/subway-wide.jpg", "tag" => "Arcade"],
            ["name" => "GTA San Andreas", "image" => "assets/img/games/gta-sa.jpg", "wide" => "assets/img/games/gta-v-wide.jpg", "tag" => "Action"],
            ["name" => "Call of Duty", "image" => "assets/img/games/cod.jpg", "wide" => "assets/img/games/pubg-wide.jpg", "tag" => "Shooter"],
            ["name" => "PUBG Battlegrounds", "image" => "assets/img/games/pubg.jpg", "wide" => "assets/img/games/pubg-wide.jpg", "tag" => "Battle Royale"],
            ["name" => "GTA Vice City", "image" => "assets/img/games/gta-vice.jpg", "wide" => "assets/img/games/gta-v-wide.jpg", "tag" => "Action"],
        ];
    }
}

if (!function_exists("millz_socials")) {
    function millz_socials()
    {
        return [
            ["id" => "instagram", "label" => "Instagram", "handle" => "@young_millz05", "url" => "https://www.instagram.com/young_millz05", "icon" => "assets/img/social/instagram.svg", "copy" => "Gameplay, store drops and behind the scenes."],
            ["id" => "tiktok", "label" => "TikTok", "handle" => "@young_millz05", "url" => "https://www.tiktok.com/@young_millz05", "icon" => "assets/img/social/tiktok.svg", "copy" => "Short clips and the latest titles."],
            ["id" => "whatsapp", "label" => "WhatsApp", "handle" => "0683179360", "url" => "https://wa.me/255683179360", "icon" => "assets/img/social/whatsapp.svg", "copy" => "Buy games, tips and request a title."],
            ["id" => "youtube", "label" => "YouTube", "handle" => "@millzjasper", "url" => "https://youtube.com/@millzjasper", "icon" => "assets/img/social/youtube.svg", "copy" => "Tutorials, gameplay and MILLZ JASPER uploads."],
        ];
    }
}

if (!function_exists("millz_social_mark")) {
    function millz_social_mark($id)
    {
        static $n = 0;
        $n++;
        $gid = "ig" . $n;
        switch ($id) {
            case "instagram":
                return '<svg class="social-mark" viewBox="0 0 24 24" aria-hidden="true"><defs><linearGradient id="' . $gid . '" x1="0" y1="1" x2="1" y2="0"><stop offset="0" stop-color="#F58529"/><stop offset=".45" stop-color="#DD2A7B"/><stop offset="1" stop-color="#515BD4"/></linearGradient></defs><rect width="24" height="24" rx="6" fill="url(#' . $gid . ')"/><circle cx="12" cy="12" r="4.15" fill="none" stroke="#fff" stroke-width="1.7"/><circle cx="16.85" cy="7.15" r="1.05" fill="#fff"/><rect x="5.4" y="5.4" width="13.2" height="13.2" rx="4.2" fill="none" stroke="#fff" stroke-width="1.7"/></svg>';
            case "tiktok":
                return '<svg class="social-mark" viewBox="0 0 24 24" aria-hidden="true"><rect width="24" height="24" rx="6" fill="#010101"/><path fill="#25F4EE" d="M13.05 4.2h2.35c.16 1.45.95 2.75 2.12 3.55.8.55 1.75.85 2.73.92v2.45c-1.4-.05-2.75-.45-3.9-1.15v6.18A5.55 5.55 0 1 1 10.7 10.6v2.48a3.07 3.07 0 1 0 3.07 3.07V4.2z"/><path fill="#FE2C55" d="M14.15 3.35h2.35c.16 1.45.95 2.75 2.12 3.55.8.55 1.75.85 2.73.92v2.45c-1.4-.05-2.75-.45-3.9-1.15v6.18A5.55 5.55 0 1 1 11.8 9.75v2.48a3.07 3.07 0 1 0 3.07 3.07V3.35z"/><path fill="#fff" d="M13.55 3.8h2.35c.16 1.45.95 2.75 2.12 3.55.8.55 1.75.85 2.73.92v2.45c-1.4-.05-2.75-.45-3.9-1.15v6.18A5.55 5.55 0 1 1 11.2 10.2v2.48a3.07 3.07 0 1 0 3.07 3.07V3.8z"/></svg>';
            case "whatsapp":
                return '<svg class="social-mark" viewBox="0 0 24 24" aria-hidden="true"><rect width="24" height="24" rx="6" fill="#25D366"/><path fill="#fff" d="M12.02 5.15a6.82 6.82 0 0 0-5.9 10.22L5.1 18.85l3.58-.94a6.82 6.82 0 1 0 3.34-12.76zm3.95 9.62c-.16.46-.95.88-1.32.94-.34.05-.77.08-1.24-.08-.29-.1-.65-.21-1.12-.41-1.97-.85-3.25-2.83-3.35-2.96-.1-.13-.84-1.12-.84-2.13s.53-1.51.72-1.72c.18-.2.4-.25.53-.25h.38c.12 0 .29-.05.45.34.16.4.55 1.38.6 1.48.05.1.08.22 0 .35-.08.13-.12.22-.24.34-.12.12-.25.27-.36.36-.12.1-.24.21-.1.41.13.2.6 1 .1.29 1.4.41 1.64.47 1.88.55.24.08.38.07.52-.08.14-.16.59-.69.75-.92.16-.24.32-.2.53-.12.22.08 1.38.65 1.62.77.24.12.4.18.46.28.05.1.05.58-.11 1.04z"/></svg>';
            case "youtube":
                return '<svg class="social-mark" viewBox="0 0 24 24" aria-hidden="true"><rect width="24" height="24" rx="6" fill="#FF0000"/><path fill="#fff" d="M9.6 8.2v7.6L16.9 12z"/></svg>';
            default:
                return "";
        }
    }
}
