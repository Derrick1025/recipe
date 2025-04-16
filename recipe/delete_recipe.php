<?php
session_start();
include '../database.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Check if the recipe belongs to the user or user is admin
    $result = $conn->query("SELECT image, user_id FROM recipes WHERE id = $id");
    $row = $result->fetch_assoc();

    if (!$row || ($user_role !== 'admin' && $row['user_id'] != $user_id)) {
        $_SESSION['message'] = 'Unauthorized to delete this recipe.';
        header("Location: ../index.php");
        exit;
    }

    // Delete the recipe
    $image = $row['image'];
    $sql = "DELETE FROM recipes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $imagePath = __DIR__ . "/uploads/" . $image;
        if (!empty($image) && file_exists($imagePath)) {
            unlink($imagePath);
        }

        $_SESSION['message'] = 'Recipe deleted successfully!';
    } else {
        $_SESSION['message'] = 'Error deleting recipe.';
    }
    header("Location: ../index.php");
    exit;
}
?>
