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
