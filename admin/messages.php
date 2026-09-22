<?php

require_once "../config/database.php";

/* =========================
   DELETE MESSAGE
========================= */

if (isset($_GET["delete"])) {

    $id = (int) $_GET["delete"];

    if ($id > 0) {

        $stmt = $pdo->prepare("
            DELETE FROM messages
            WHERE id = ?
        ");

        $stmt->execute([$id]);
    }

    header("Location: messages.php?deleted=1");
    exit;
}


/* =========================
   GET MESSAGES
========================= */

try {

    $stmt = $pdo->query("
        SELECT *
        FROM messages
        ORDER BY id DESC
    ");

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    die("Could not load messages.");

}


/* =========================
   COLUMNS
========================= */

$columns = [];

if (!empty($messages)) {
    $columns = array_keys($messages[0]);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>MILLZ GAMES - Messages</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #080b10;
    color: #fff;
    font-family: Arial, sans-serif;
}

.header {
    background: #0d1219;
    border-bottom: 1px solid #1d2630;
    padding: 18px 25px;
}

.header h1 {
    margin: 0;
    color: #00ff88;
    font-size: 25px;
}

.header p {
    margin: 5px 0 0;
    color: #8d98a5;
}

.container {
    width: 95%;
    max-width: 1400px;
    margin: 30px auto;
}

.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.top-bar h2 {
    margin: 0;
}

.top-bar p {
    color: #8d98a5;
    margin: 6px 0 0;
}

.back-btn {
    text-decoration: none;
    color: #00ff88;
    border: 1px solid #00ff88;
    padding: 10px 16px;
    border-radius: 8px;
}

.back-btn:hover {
    background: #00ff88;
    color: #000;
}

.info-card {
    background: #0d1219;
    border: 1px solid #1d2630;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
}

.info-title {
    color: #8d98a5;
    font-size: 14px;
}

.info-number {
    color: #00ff88;
    font-size: 28px;
    font-weight: bold;
    margin-top: 7px;
}

.table-box {
    background: #0d1219;
    border: 1px solid #1d2630;
    border-radius: 12px;
    overflow: hidden;
}

.table-scroll {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 900px;
    border-collapse: collapse;
}

th {
    background: #111821;
    color: #00ff88;
    text-align: left;
    padding: 14px;
    border-bottom: 1px solid #26313d;
    white-space: nowrap;
}

td {
    padding: 13px 14px;
    border-bottom: 1px solid #1a222b;
    color: #dce3ea;
    vertical-align: top;
}

tr:hover td {
    background: #10161e;
}

.message-cell {
    white-space: normal;
    min-width: 250px;
    max-width: 450px;
    line-height: 1.5;
}

.delete-btn {
    display: inline-block;
    text-decoration: none;
    background: #ff3b3b;
    color: #fff;
    padding: 8px 12px;
    border-radius: 7px;
    font-size: 13px;
}

.delete-btn:hover {
    background: #d90000;
}

.empty {
    text-align: center;
    padding: 55px 20px;
    color: #8d98a5;
}

.empty h3 {
    color: #fff;
}

.success {
    background: rgba(0, 255, 136, 0.08);
    border: 1px solid #00ff88;
    color: #00ff88;
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

@media (max-width: 700px) {

    .container {
        width: 94%;
        margin: 20px auto;
    }

    .header {
        padding: 16px;
    }

    .header h1 {
        font-size: 21px;
    }

}

</style>

</head>

<body>


<div class="header">

    <h1>MILLZ GAMES</h1>

    <p>Customer Messages & Support</p>

</div>


<div class="container">


    <div class="top-bar">

        <div>

            <h2>Messages 💬</h2>

            <p>
                View customer questions and support messages.
            </p>

        </div>

        <a href="index.php" class="back-btn">
            ← Dashboard
        </a>

    </div>


    <?php if (isset($_GET["deleted"])): ?>

        <div class="success">
            Message deleted successfully.
        </div>

    <?php endif; ?>


    <div class="info-card">

        <div class="info-title">
            TOTAL MESSAGES
        </div>

        <div class="info-number">
            <?= count($messages); ?>
        </div>

    </div>


    <div class="table-box">

        <?php if (empty($messages)): ?>

            <div class="empty">

                <h3>No Messages Yet</h3>

                <p>
                    Customer messages will appear here.
                </p>

            </div>

        <?php else: ?>

            <div class="table-scroll">

                <table>

                    <thead>

                        <tr>

                            <?php foreach ($columns as $column): ?>

                                <th>

                                    <?= htmlspecialchars(
                                        ucwords(
                                            str_replace(
                                                "_",
                                                " ",
                                                $column
                                            )
                                        )
                                    ); ?>

                                </th>

                            <?php endforeach; ?>

                            <th>
                                ACTION
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($messages as $message): ?>

                            <tr>

                                <?php foreach ($columns as $column): ?>

                                    <td class="<?= (
                                        stripos(
                                            $column,
                                            "message"
                                        ) !== false ||
                                        stripos(
                                            $column,
                                            "content"
                                        ) !== false
                                    ) ? "message-cell" : ""; ?>">

                                        <?= htmlspecialchars(
                                            (string)(
                                                $message[$column]
                                                ?? ""
                                            )
                                        ); ?>

                                    </td>

                                <?php endforeach; ?>


                                <td>

                                    <a
                                        href="messages.php?delete=<?= (int)$message["id"]; ?>"
                                        class="delete-btn"
                                        onclick="return confirm('Delete this message?');"
                                    >
                                        Delete
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>


</div>

</body>

</html>