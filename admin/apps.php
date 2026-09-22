<?php
require_once "../config/database.php";

$stmt = $pdo->query("
    SELECT 
        a.*,
        c.name AS category_name
    FROM apps a
    LEFT JOIN categories c ON a.category_id = c.id
    ORDER BY a.created_at DESC
");

$apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>MILLZ GAMES — Apps</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;600;700;800&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">

<style>

*{
    box-sizing:border-box;
    margin:0;
    padding:0;
}

body{
    background:#080a0f;
    color:#fff;
    font-family:'Rajdhani',sans-serif;
}

.page{
    max-width:1250px;
    margin:auto;
    padding:25px 18px 60px;
}

.top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    margin-bottom:25px;
    flex-wrap:wrap;
}

.title h1{
    font-family:'Orbitron',sans-serif;
    color:#00ff88;
    font-size:25px;
}

.title p{
    color:#7d8795;
    margin-top:5px;
}

.add{
    background:#00ff88;
    color:#06100b;
    text-decoration:none;
    padding:12px 18px;
    border-radius:9px;
    font-weight:800;
}

.back{
    display:inline-block;
    color:#8993a1;
    text-decoration:none;
    margin-bottom:18px;
}

.back:hover{
    color:#00ff88;
}

.table-box{
    background:#10141c;
    border:1px solid #202632;
    border-radius:14px;
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
    min-width:900px;
}

th,
td{
    padding:14px;
    text-align:left;
    border-bottom:1px solid #202632;
}

th{
    color:#8994a4;
    font-family:'Orbitron',sans-serif;
    font-size:11px;
    letter-spacing:.5px;
}

td{
    font-weight:600;
}

.cover{
    width:55px;
    height:55px;
    object-fit:cover;
    border-radius:9px;
    background:#080a0f;
    border:1px solid #2a313d;
}

.no-image{
    width:55px;
    height:55px;
    border-radius:9px;
    background:#171c25;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#697383;
    font-size:11px;
}

.status{
    display:inline-block;
    padding:5px 9px;
    border-radius:20px;
    font-size:12px;
}

.published{
    background:#00ff8815;
    color:#00ff88;
}

.unpublished{
    background:#ffb02015;
    color:#ffbd4a;
}

.featured{
    color:#00ff88;
}

.not-featured{
    color:#626d7c;
}

.actions{
    display:flex;
    gap:7px;
    flex-wrap:wrap;
}

.action{
    text-decoration:none;
    padding:7px 10px;
    border-radius:7px;
    background:#202632;
    color:#fff;
    font-size:13px;
}

.action:hover{
    background:#00ff88;
    color:#06100b;
}

.empty{
    padding:50px 20px;
    text-align:center;
    color:#7c8795;
}

.empty h2{
    color:#fff;
    margin-bottom:8px;
}

@media(max-width:700px){

    .page{
        padding:18px 10px 50px;
    }

    .top{
        align-items:flex-start;
    }

    .add{
        width:100%;
        text-align:center;
    }

}

</style>

</head>

<body>

<div class="page">

<a href="index.php" class="back">← Admin Dashboard</a>

<div class="top">

<div class="title">
<h1>APPS</h1>
<p>Manage all applications in MILLZ GAMES.</p>
</div>

<a href="add-app.php" class="add">
+ ADD NEW APP
</a>

</div>


<?php if(empty($apps)): ?>

<div class="table-box">

<div class="empty">

<h2>No Apps Yet</h2>

<p>
Add your first application using the button above.
</p>

</div>

</div>

<?php else: ?>


<div class="table-box">

<table>

<thead>

<tr>

<th>Cover</th>
<th>APP</th>
<th>Category</th>
<th>Platform</th>
<th>Price</th>
<th>Status</th>
<th>Featured</th>
<th>Actions</th>

</tr>

</thead>

<tbody>

<?php foreach($apps as $app): ?>

<tr>

<td>

<?php if(!empty($app['cover_image'])): ?>

<img
src="../uploads/covers/<?= htmlspecialchars($app['cover_image']) ?>"
class="cover"
alt="App Cover"
>

<?php else: ?>

<div class="no-image">
NO IMAGE
</div>

<?php endif; ?>

</td>


<td>

<strong>
<?= htmlspecialchars($app['name']) ?>
</strong>

</td>


<td>

<?= htmlspecialchars($app['category_name'] ?? 'Uncategorized') ?>

</td>


<td>

<?= htmlspecialchars($app['platform'] ?? 'Not set') ?>

</td>


<td>

<?php if($app['price'] !== null && $app['price'] !== ''): ?>

TSh <?= number_format((float)$app['price'], 0) ?>

<?php else: ?>

<span style="color:#697383;">
Not set
</span>

<?php endif; ?>

</td>


<td>

<?php if($app['status'] === 'published'): ?>

<span class="status published">
Published
</span>

<?php else: ?>

<span class="status unpublished">
Unpublished
</span>

<?php endif; ?>

</td>


<td>

<?php if(!empty($app['featured'])): ?>

<span class="featured">
⭐ YES
</span>

<?php else: ?>

<span class="not-featured">
NO
</span>

<?php endif; ?>

</td>


<td>

<div class="actions">

<a
href="edit-app.php?id=<?= (int)$app['id'] ?>"
class="action"
>
Edit
</a>

<a
href="delete-app.php?id=<?= (int)$app['id'] ?>"
class="action"
onclick="return confirm('Delete this app?');"
>
Delete
</a>

<a
href="feature-app.php?id=<?= (int)$app['id'] ?>"
class="action"
>
<?= !empty($app['featured']) ? 'Unfeature' : 'Feature' ?>
</a>

</div>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

<?php endif; ?>

</div>

</body>
</html>