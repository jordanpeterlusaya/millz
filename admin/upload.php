<?php
session_start();

// Database connection based on your project structure
require_once '../config/database.php'; 

// Check authentication if required
if (file_exists('includes/auth.php')) {
    require_once 'includes/auth.php';
}

$message = "";

if (isset($_POST['submit'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $category = $_POST['category']; // 'game' or 'app'
    $platform = $conn->real_escape_string($_POST['platform']); 
    $price = $_POST['price'];
    $description = $conn->real_escape_string($_POST['description']);

    // Handling Image Upload
    $imagePath = "";
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $targetImage = "../images/" . $imageName;
        
        if (!file_exists('../images')) {
            mkdir('../images', 0777, true);
        }
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetImage)) {
            $imagePath = "images/" . $imageName;
        }
    }

    // Handling App / Game File Upload (APK, ZIP, RAR, etc.)
    $filePath = "";
    if (!empty($_FILES['file']['name'])) {
        $fileName = time() . '_' . basename($_FILES['file']['name']);
        $targetFile = "../downloads/" . $fileName;
        
        if (!file_exists('../downloads')) {
            mkdir('../downloads', 0777, true);
        }
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            $filePath = "downloads/" . $fileName;
        }
    }

    // Insert record into database
    $sql = "INSERT INTO products (title, category, platform, price, description, image, file_path) 
            VALUES ('$title', '$category', '$platform', '$price', '$description', '$imagePath', '$filePath')";

    if ($conn->query($sql)) {
        $message = "<div class='alert success'>✅ App or Game uploaded successfully!</div>";
    } else {
        $message = "<div class='alert danger'>❌ Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload App / Game - MILLZ GAMES</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #0b0c10;
            color: #c5c6c7;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .upload-container {
            max-width: 650px;
            margin: 30px auto;
            background: #1f2833;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.6);
            border: 1px solid #334155;
        }

        .upload-container h2 {
            color: #fff;
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .upload-container h2 span {
            color: #39ff14;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #66fcf1;
            font-weight: 600;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea,
        .form-group input[type="file"] {
            width: 100%;
            padding: 12px;
            background: #0b0c10;
            border: 1px solid #45a29e;
            border-radius: 6px;
            color: #fff;
            font-size: 15px;
            box-sizing: border-box;
            outline: none;
        }

        .form-group input:focus, 
        .form-group select:focus, 
        .form-group textarea:focus {
            border-color: #39ff14;
            box-shadow: 0 0 8px rgba(57, 255, 20, 0.3);
        }

        .btn-submit {
            width: 100%;
            background-color: #39ff14;
            color: #000;
            padding: 14px;
            border: none;
            border-radius: 6px;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background-color: #32cd12;
            box-shadow: 0 0 15px rgba(57, 255, 20, 0.5);
        }

        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        .alert.success {
            background-color: rgba(57, 255, 20, 0.15);
            color: #39ff14;
            border: 1px solid #39ff14;
        }

        .alert.danger {
            background-color: rgba(255, 0, 0, 0.15);
            color: #ff4d4d;
            border: 1px solid #ff4d4d;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 15px;
            color: #66fcf1;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="upload-container">
        <a href="dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        <h2>Upload <span>App or Game</span></h2>

        <?= $message ?>

        <form action="" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label><i class="fas fa-gamepad"></i> App / Game Title:</label>
                <input type="text" name="title" placeholder="e.g. GTA V, eFootball, CapCut Pro..." required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-list"></i> Category:</label>
                <select name="category" required>
                    <option value="game">Game</option>
                    <option value="app">Android App / Tool</option>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fas fa-desktop"></i> Platform:</label>
                <input type="text" name="platform" placeholder="e.g. Android, PC, PS4, PS5" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-tag"></i> Price (TSH):</label>
                <input type="number" name="price" placeholder="Set 0 for FREE" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Description:</label>
                <textarea name="description" rows="4" placeholder="Brief description of the app or game..."></textarea>
            </div>

            <div class="form-group">
                <label><i class="fas fa-image"></i> Cover / Poster Image:</label>
                <input type="file" name="image" accept="image/*" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-file-archive"></i> App/Game File (APK / ZIP / RAR):</label>
                <input type="file" name="file">
            </div>

            <button type="submit" name="submit" class="btn-submit"><i class="fas fa-cloud-upload-alt"></i> UPLOAD NOW</button>
        </form>
    </div>

</body>
</html>