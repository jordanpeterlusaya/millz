<?php

require_once "../config/database.php";

/* =========================
   GET SALES
========================= */

try {

    $stmt = $pdo->query("
        SELECT *
        FROM sales
        ORDER BY id DESC
    ");

    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    die("Could not load sales.");

}


/* =========================
   COLUMNS
========================= */

$columns = [];

if (!empty($sales)) {
    $columns = array_keys($sales[0]);
}


/* =========================
   TOTAL AMOUNT
========================= */

$totalAmount = 0;

foreach ($sales as $sale) {

    if (
        isset($sale["amount"]) &&
        is_numeric($sale["amount"])
    ) {
        $totalAmount += (float)$sale["amount"];
    }

}

?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>MILLZ GAMES - Sales</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #080b10;
    color: #ffffff;
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

.back-btn {
    text-decoration: none;
    color: #00ff88;
    border: 1px solid #00ff88;
    padding: 10px 16px;
    border-radius: 8px;
    display: inline-block;
}

.back-btn:hover {
    background: #00ff88;
    color: #000;
}

.cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
    margin-bottom: 25px;
}

.card {
    background: #0d1219;
    border: 1px solid #1d2630;
    border-radius: 12px;
    padding: 20px;
}

.card-title {
    color: #8d98a5;
    font-size: 14px;
    margin-bottom: 8px;
}

.card-value {
    color: #00ff88;
    font-size: 28px;
    font-weight: bold;
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
    min-width: 800px;
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
    white-space: nowrap;
}

tr:hover td {
    background: #10161e;
}

.empty {
    text-align: center;
    padding: 50px 20px;
    color: #8d98a5;
}

@media (max-width: 700px) {

    .container {
        width: 94%;
        margin: 20px auto;
    }

    .cards {
        grid-template-columns: 1fr;
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
    <p>Sales Management</p>
</div>

<div class="container">

    <div class="top-bar">

        <div>
            <h2 style="margin:0;">Sales 💰</h2>
            <p style="color:#8d98a5;">
                View all recorded purchases and sales.
            </p>
        </div>

        <a href="index.php" class="back-btn">
            ← Dashboard
        </a>

    </div>


    <div class="cards">

        <div class="card">
            <div class="card-title">
                TOTAL SALES
            </div>

            <div class="card-value">
                <?= count($sales); ?>
            </div>
        </div>


        <div class="card">
            <div class="card-title">
                TOTAL AMOUNT
            </div>

            <div class="card-value">
                TSh <?= number_format($totalAmount, 2); ?>
            </div>
        </div>

    </div>


    <div class="table-box">

        <?php if (empty($sales)): ?>

            <div class="empty">
                <h3>No Sales Yet</h3>
                <p>
                    Sales will appear here when customers make purchases.
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

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($sales as $sale): ?>

                            <tr>

                                <?php foreach ($columns as $column): ?>

                                    <td>

                                        <?php

                                        $value = $sale[$column] ?? "";

                                        if (
                                            $column === "amount" &&
                                            is_numeric($value)
                                        ) {

                                            echo "TSh " .
                                                number_format(
                                                    (float)$value,
                                                    2
                                                );

                                        } else {

                                            echo htmlspecialchars(
                                                (string)$value
                                            );

                                        }

                                        ?>

                                    </td>

                                <?php endforeach; ?>

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