<?php
session_start();
include '../database.php';

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php?message=deleted");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Get the image filename before deleting the recipe
    $result = $conn->query("SELECT image FROM recipes WHERE id = $id");
    $row = $result->fetch_assoc();
    $image = $row['image'];

    // Delete the recipe from the database
    $sql = "DELETE FROM recipes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Delete the image file from the uploads folder
        $imagePath = __DIR__ . "/uploads/" . $image;
        if (!empty($image) && file_exists($imagePath)) {
            unlink($imagePath);
        }

        $_SESSION['message'] = 'Recipe deleted successfully!';
        header("Location: index.php?message=deleted");
        exit;
    } else {
        die("Error deleting recipe: " . $conn->error);
    }
} else {
    die("Invalid request.");
}
