<?php

require_once "../config/database.php";

$message = "";
$error = "";

/* =========================
   GENERATE ACCESS CODE
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $game_id = !empty($_POST["game_id"])
        ? (int) $_POST["game_id"]
        : null;

    $app_id = !empty($_POST["app_id"])
        ? (int) $_POST["app_id"]
        : null;

    if (!$game_id && !$app_id) {

        $error = "Select a Game or App first.";

    } elseif ($game_id && $app_id) {

        $error = "Select only one: Game OR App.";

    } else {

        try {

            /* Generate unique code */
            do {

                $code = "MILLZ" . strtoupper(
                    substr(bin2hex(random_bytes(4)), 0, 6)
                );

                $check = $pdo->prepare("
                    SELECT id
                    FROM access_codes
                    WHERE code = ?
                    LIMIT 1
                ");

                $check->execute([$code]);

            } while ($check->fetch());

            /*
             * Your database has expires_at as NOT NULL.
             * Therefore every new code must receive
             * an expiry date when it is created.
             *
             * Access period = 24 hours.
             */

            $expires_at = date(
                "Y-m-d H:i:s",
                strtotime("+24 hours")
            );

            $insert = $pdo->prepare("
                INSERT INTO access_codes
                (
                    code,
                    game_id,
                    app_id,
                    expires_at,
                    status
                )
                VALUES (?, ?, ?, ?, 'active')
            ");

            $insert->execute([
                $code,
                $game_id,
                $app_id,
                $expires_at
            ]);

            $message = "ACCESS CODE GENERATED: " . $code;

        } catch (PDOException $e) {

            $error = "Could not generate access code.";

        }
    }
}


/* =========================
   DISABLE CODE
========================= */

if (isset($_GET["disable"])) {

    $id = (int) $_GET["disable"];

    if ($id > 0) {

        $stmt = $pdo->prepare("
            UPDATE access_codes
            SET status = 'disabled'
            WHERE id = ?
        ");

        $stmt->execute([$id]);
    }

    header("Location: access-codes.php");
    exit;
}


/* =========================
   DELETE CODE
========================= */

if (isset($_GET["delete"])) {

    $id = (int) $_GET["delete"];

    if ($id > 0) {

        $stmt = $pdo->prepare("
            DELETE FROM access_codes
            WHERE id = ?
        ");

        $stmt->execute([$id]);
    }

    header("Location: access-codes.php");
    exit;
}


/* =========================
   AUTO MARK EXPIRED CODES
========================= */

$pdo->query("
    UPDATE access_codes
    SET status = 'expired'
    WHERE expires_at <= NOW()
    AND status = 'active'
");


/* =========================
   GET GAMES
========================= */

$gamesStmt = $pdo->query("
    SELECT id, name
    FROM games
    WHERE status = 'published'
    ORDER BY name ASC
");

$games = $gamesStmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   GET APPS
========================= */

$appsStmt = $pdo->query("
    SELECT id, name
    FROM apps
    WHERE status = 'published'
    ORDER BY name ASC
");

$apps = $appsStmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   GET ACCESS CODES
========================= */

$codesStmt = $pdo->query("
    SELECT
        ac.*,
        g.name AS game_name,
        a.name AS app_name
    FROM access_codes ac
    LEFT JOIN games g
        ON ac.game_id = g.id
    LEFT JOIN apps a
        ON ac.app_id = a.id
    ORDER BY ac.created_at DESC
");

$codes = $codesStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Access Codes | MILLZ GAMES</title>

<link
    href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;600;700;800&family=Rajdhani:wght@500;600;700&display=swap"
    rel="stylesheet"
>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #070b0f;
    color: #ffffff;
    font-family: "Rajdhani", sans-serif;
}

.container {
    width: min(1200px, 94%);
    margin: 30px auto;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.title {
    font-family: "Orbitron", sans-serif;
    color: #00ff88;
    font-size: 25px;
    font-weight: 800;
}

.back {
    text-decoration: none;
    color: #ffffff;
    background: #111820;
    border: 1px solid #26313a;
    padding: 10px 18px;
    border-radius: 8px;
}

.box {
    background: #0d1319;
    border: 1px solid #1d2932;
    border-radius: 14px;
    padding: 22px;
    margin-bottom: 25px;
}

h2 {
    font-family: "Orbitron", sans-serif;
    font-size: 18px;
    margin-top: 0;
    color: #00ff88;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

label {
    display: block;
    margin-bottom: 7px;
    font-weight: 700;
}

select {
    width: 100%;
    padding: 13px;
    background: #070b0f;
    border: 1px solid #293640;
    border-radius: 8px;
    color: #ffffff;
    font-size: 16px;
}

button {
    margin-top: 18px;
    padding: 13px 20px;
    border: none;
    border-radius: 8px;
    background: #00ff88;
    color: #04100a;
    font-weight: 800;
    font-family: "Orbitron", sans-serif;
    cursor: pointer;
}

.message {
    background: #073d27;
    border: 1px solid #00ff88;
    padding: 13px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-weight: 700;
}

.error {
    background: #42151a;
    border: 1px solid #ff4d5a;
    padding: 13px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-weight: 700;
}

.generated {
    font-family: "Orbitron", sans-serif;
    font-size: 22px;
    color: #00ff88;
    margin-top: 8px;
    word-break: break-word;
}

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 850px;
}

th,
td {
    padding: 13px;
    border-bottom: 1px solid #1d2932;
    text-align: left;
}

th {
    color: #00ff88;
    font-family: "Orbitron", sans-serif;
    font-size: 12px;
}

.code {
    color: #00ff88;
    font-family: "Orbitron", sans-serif;
    font-weight: 700;
}

.status {
    padding: 5px 9px;
    border-radius: 6px;
    font-weight: 700;
    display: inline-block;
}

.active {
    background: #073d27;
    color: #00ff88;
}

.used {
    background: #27354a;
    color: #8fb8ff;
}

.expired {
    background: #422b0d;
    color: #ffb84d;
}

.disabled {
    background: #42151a;
    color: #ff6670;
}

.action {
    color: #ffffff;
    text-decoration: none;
    padding: 6px 10px;
    border-radius: 6px;
    background: #18212a;
    margin-right: 5px;
    display: inline-block;
}

.action.delete {
    background: #42151a;
    color: #ff6670;
}

@media (max-width: 700px) {

    .container {
        width: 92%;
        margin: 20px auto;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .title {
        font-size: 20px;
    }
}

</style>

</head>

<body>

<div class="container">

    <div class="topbar">

        <div class="title">
            MILLZ GAMES — ACCESS CODES
        </div>

        <a
            href="index.php"
            class="back"
        >
            ← Dashboard
        </a>

    </div>


    <?php if ($message): ?>

        <div class="message">

            <?= htmlspecialchars($message) ?>

            <div class="generated">

                <?= htmlspecialchars(
                    str_replace(
                        "ACCESS CODE GENERATED: ",
                        "",
                        $message
                    )
                ) ?>

            </div>

        </div>

    <?php endif; ?>


    <?php if ($error): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <div class="box">

        <h2>
            GENERATE NEW ACCESS CODE
        </h2>

        <form method="POST">

            <div class="form-grid">

                <div>

                    <label>
                        Select Game
                    </label>

                    <select name="game_id">

                        <option value="">
                            -- No Game --
                        </option>

                        <?php foreach ($games as $game): ?>

                            <option
                                value="<?= (int) $game["id"] ?>"
                            >
                                <?= htmlspecialchars(
                                    $game["name"]
                                ) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div>

                    <label>
                        Select App
                    </label>

                    <select name="app_id">

                        <option value="">
                            -- No App --
                        </option>

                        <?php foreach ($apps as $app): ?>

                            <option
                                value="<?= (int) $app["id"] ?>"
                            >
                                <?= htmlspecialchars(
                                    $app["name"]
                                ) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>


            <button type="submit">
                GENERATE ACCESS CODE
            </button>

        </form>

        <p>
            New access codes are active for 24 hours from the time they are generated.
        </p>

    </div>


    <div class="box">

        <h2>
            ALL ACCESS CODES
        </h2>

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>

                        <th>CODE</th>
                        <th>PRODUCT</th>
                        <th>STATUS</th>
                        <th>EXPIRES</th>
                        <th>CREATED</th>
                        <th>ACTIONS</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (!$codes): ?>

                        <tr>

                            <td colspan="6">
                                No access codes yet.
                            </td>

                        </tr>

                    <?php else: ?>

                        <?php foreach ($codes as $row): ?>

                            <?php

                            $product = "Unknown";

                            if (!empty($row["game_name"])) {

                                $product =
                                    "Game: " .
                                    $row["game_name"];

                            } elseif (!empty($row["app_name"])) {

                                $product =
                                    "App: " .
                                    $row["app_name"];

                            }

                            ?>

                            <tr>

                                <td class="code">

                                    <?= htmlspecialchars(
                                        $row["code"]
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $product
                                    ) ?>

                                </td>


                                <td>

                                    <span
                                        class="status <?= htmlspecialchars(
                                            $row["status"]
                                        ) ?>"
                                    >

                                        <?= strtoupper(
                                            htmlspecialchars(
                                                $row["status"]
                                            )
                                        ) ?>

                                    </span>

                                </td>


                                <td>

                                    <?= !empty(
                                        $row["expires_at"]
                                    )
                                        ? htmlspecialchars(
                                            $row["expires_at"]
                                        )
                                        : "N/A"
                                    ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $row["created_at"]
                                    ) ?>

                                </td>


                                <td>

                                    <?php if (
                                        $row["status"] === "active"
                                    ): ?>

                                        <a
                                            class="action"
                                            href="access-codes.php?disable=<?= (int) $row["id"] ?>"
                                            onclick="return confirm('Disable this access code?');"
                                        >
                                            Disable
                                        </a>

                                    <?php endif; ?>


                                    <a
                                        class="action delete"
                                        href="access-codes.php?delete=<?= (int) $row["id"] ?>"
                                        onclick="return confirm('Delete this access code permanently?');"
                                    >
                                        Delete
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>

</html>