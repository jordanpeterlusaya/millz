<?php

require_once "../config/database.php";

/* =========================
   DELETE CUSTOMER
========================= */

if (isset($_GET["delete"])) {

    $id = (int)$_GET["delete"];

    if ($id > 0) {

        try {

            $stmt = $pdo->prepare("
                DELETE FROM customers
                WHERE id = ?
            ");

            $stmt->execute([$id]);

        } catch (PDOException $e) {

            $error = "Customer could not be deleted.";

        }
    }

    header("Location: customers.php");
    exit;
}


/* =========================
   SEARCH
========================= */

$search = trim($_GET["search"] ?? "");


/* =========================
   GET CUSTOMERS
========================= */

if ($search !== "") {

    $stmt = $pdo->prepare("
        SELECT *
        FROM customers
        WHERE
            name LIKE ?
            OR email LIKE ?
            OR phone LIKE ?
        ORDER BY created_at DESC
    ");

    $term = "%" . $search . "%";

    $stmt->execute([
        $term,
        $term,
        $term
    ]);

} else {

    $stmt = $pdo->query("
        SELECT *
        FROM customers
        ORDER BY created_at DESC
    ");
}

$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   TOTAL CUSTOMERS
========================= */

$totalCustomers = count($customers);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>MILLZ GAMES - Customers</title>

<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;600;700&family=Rajdhani:wght@500;600;700&display=swap"
      rel="stylesheet">

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #070b0f;
    color: #fff;
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
}

.title {
    font-family: "Orbitron", sans-serif;
    font-size: 25px;
    color: #00ff88;
}

.back {
    text-decoration: none;
    color: #fff;
    background: #111820;
    border: 1px solid #26333d;
    padding: 10px 16px;
    border-radius: 8px;
}

.back:hover {
    color: #00ff88;
    border-color: #00ff88;
}

.summary {
    background: #0d1319;
    border: 1px solid #1d2932;
    border-radius: 12px;
    padding: 18px;
    margin-bottom: 20px;
}

.summary strong {
    color: #00ff88;
    font-size: 25px;
}

.search-box {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.search-box input {
    flex: 1;
    min-width: 0;
    padding: 13px;
    border-radius: 8px;
    border: 1px solid #26333d;
    background: #0d1319;
    color: #fff;
    outline: none;
    font-size: 16px;
}

.search-box input:focus {
    border-color: #00ff88;
}

.search-box button {
    padding: 12px 20px;
    border: 0;
    border-radius: 8px;
    background: #00ff88;
    color: #06100b;
    font-weight: 700;
    cursor: pointer;
}

.table-card {
    background: #0d1319;
    border: 1px solid #1d2932;
    border-radius: 12px;
    overflow: hidden;
}

.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 750px;
    border-collapse: collapse;
}

th,
td {
    padding: 14px;
    border-bottom: 1px solid #1d2932;
    text-align: left;
}

th {
    background: #111820;
    color: #00ff88;
    font-family: "Orbitron", sans-serif;
    font-size: 12px;
}

td {
    color: #d9e0e5;
}

tr:hover td {
    background: #10171d;
}

.badge {
    display: inline-block;
    padding: 5px 9px;
    border-radius: 6px;
    background: rgba(0,255,136,.10);
    color: #00ff88;
    font-size: 13px;
}

.delete {
    display: inline-block;
    text-decoration: none;
    background: #38151a;
    color: #ff6b6b;
    border: 1px solid #66242c;
    padding: 7px 11px;
    border-radius: 6px;
}

.delete:hover {
    background: #ff4d4d;
    color: #fff;
}

.empty {
    padding: 35px;
    text-align: center;
    color: #8d9aa4;
}

@media (max-width: 600px) {

    .container {
        width: 94%;
        margin: 20px auto;
    }

    .topbar {
        align-items: flex-start;
        flex-direction: column;
    }

    .title {
        font-size: 21px;
    }

    .search-box {
        flex-direction: column;
    }

    .search-box button {
        width: 100%;
    }
}

</style>

</head>

<body>

<div class="container">

    <div class="topbar">

        <div class="title">
            MILLZ GAMES 👥
        </div>

        <a href="index.php" class="back">
            ← Dashboard
        </a>

    </div>


    <div class="summary">

        <div>
            TOTAL CUSTOMERS
        </div>

        <strong>
            <?= $totalCustomers ?>
        </strong>

    </div>


    <form method="GET" class="search-box">

        <input
            type="text"
            name="search"
            value="<?= htmlspecialchars($search) ?>"
            placeholder="Search customer name, email or phone..."
        >

        <button type="submit">
            🔎 SEARCH
        </button>

    </form>


    <div class="table-card">

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>NAME</th>

                        <th>EMAIL</th>

                        <th>PHONE</th>

                        <th>JOINED</th>

                        <th>ACTION</th>

                    </tr>

                </thead>

                <tbody>

                <?php if (!empty($customers)): ?>

                    <?php foreach ($customers as $customer): ?>

                        <tr>

                            <td>
                                #<?= (int)$customer["id"] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($customer["name"] ?? "—") ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($customer["email"] ?? "—") ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($customer["phone"] ?? "—") ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($customer["created_at"] ?? "—") ?>
                            </td>

                            <td>

                                <a
                                    class="delete"
                                    href="customers.php?delete=<?= (int)$customer["id"] ?>"
                                    onclick="return confirm('Delete this customer?');"
                                >
                                    🗑 Delete
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="6" class="empty">
                            No customers found.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>

</html>