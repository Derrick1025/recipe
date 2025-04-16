<?php
session_start();
include '../database.php';
include '../navbar.php';

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

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

    // Validate image upload: Check if an image file was uploaded
    if (empty($_FILES['image']['name'])) {
        $_SESSION['message'] = 'Please upload an image for the recipe.';
        header("Location: add_recipe.php");
        exit;
    }

    // Validate the image format (only allow JPG, JPEG, PNG, and GIF)
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

    $fileExtension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $fileMimeType = mime_content_type($_FILES["image"]["tmp_name"]);

    if (!in_array($fileExtension, $allowedExtensions)) {
        $_SESSION['message'] = 'Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.';
        header("Location: add_recipe.php");
        exit;
    }

    if (!in_array($fileMimeType, $allowedMimeTypes)) {
        $_SESSION['message'] = 'Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.';
        header("Location: add_recipe.php");
        exit;
    }

    // Process the image upload and save it
    $targetDir = "uploads/";
    $image = basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $image;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        // Insert recipe into the database
        $sql = "INSERT INTO recipes (title, ingredients, steps, category, image) 
                VALUES ('$title', '$ingredients', '$steps', '$category', '$image')";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = 'Recipe added successfully!';
            header("Location: ../index.php"); // Redirect to the recipe list
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
</head>

<body class="bg-light">
    <div class="container mt-4">
        <h2 class="mb-4">Add Recipe</h2>

        <!-- Display success or error message -->
        <?php
        if (isset($_SESSION['message'])) {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    <i class='bi bi-exclamation-circle'></i> " . $_SESSION['message'] . "
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";
            unset($_SESSION['message']);
        }
        ?>

        <form method="POST" enctype="multipart/form-data" class="card p-4 shadow">
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
                <select name="category" class="form-control" required>
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
                <button type="submit" class="btn btn-success">Add Recipe</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
