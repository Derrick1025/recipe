<?php
session_start();
include '../database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $ingredients = $_POST['ingredients'];
    $steps = $_POST['steps'];
    $category = $_POST['category'];
    $image = null;

    $user_id = $_SESSION['user_id'];

    if (empty($_FILES['image']['name'])) {
        $_SESSION['message'] = 'Please upload an image for the recipe.';
        header("Location: add_recipe.php");
        exit;
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileExtension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $fileMimeType = mime_content_type($_FILES["image"]["tmp_name"]);

    if (!in_array($fileExtension, $allowedExtensions) || !in_array($fileMimeType, $allowedMimeTypes)) {
        $_SESSION['message'] = 'Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.';
        header("Location: add_recipe.php");
        exit;
    }

    $targetDir = "uploads/";
    $image = basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $image;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $sql = "INSERT INTO recipes (title, ingredients, steps, category, image, user_id) 
                VALUES ('$title', '$ingredients', '$steps', '$category', '$image', '$user_id')";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = 'Recipe added successfully!';
            header("Location: ../index.php");
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        $_SESSION['message'] = 'Error uploading the image.';
        header("Location: add_recipe.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Recipe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            background: linear-gradient(135deg, #c3ecf4, #a0c3ff);
            overflow-x: hidden;
            font-family: 'Segoe UI', sans-serif;
        }
        .wave {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 150px;
            background: url('https://svgshare.com/i/vUe.svg') repeat-x;
            background-size: cover;
            animation: waveMove 10s linear infinite;
            opacity: 0.3;
            z-index: 0;
        }
        @keyframes waveMove {
            0% { background-position-x: 0; }
            100% { background-position-x: 1000px; }
        }
        .form-wrapper {
            position: relative;
            z-index: 1;
        }
        .card {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.85);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            animation: fadeSlide 0.4s ease-in-out;
        }
        @keyframes fadeSlide {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>
    <?php include '../navbar.php'; ?>

    <div class="d-flex justify-content-center align-items-center vh-100 form-wrapper">
        <div class="card p-4 shadow-lg" style="max-width: 600px; width: 100%;">
            <h3 class="text-center mb-4"><i class="bi bi-journal-plus"></i> Add a New Recipe</h3>

            <?php if (isset($_SESSION['message'])): ?>
                <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    <i class='bi bi-exclamation-circle'></i> <?= $_SESSION['message']; ?>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ingredients</label>
                    <textarea class="form-control" name="ingredients" required rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Steps</label>
                    <textarea class="form-control" name="steps" required rows="4"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Category (Cuisine)</label>
                    <select name="category" class="form-select" required>
                        <option value="Italian">Italian</option>
                        <option value="Chinese">Chinese</option>
                        <option value="Mexican">Mexican</option>
                        <option value="Indian">Indian</option>
                        <option value="Japanese">Japanese</option>
                        <option value="French">French</option>
                        <option value="Mediterranean">Mediterranean</option>
                        <option value="Thai">Thai</option>
                        <option value="Korean">Korean</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control" required>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success w-100">Add Recipe</button>
                    <a href="../index.php" class="btn btn-outline-secondary w-100">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <div class="wave"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
