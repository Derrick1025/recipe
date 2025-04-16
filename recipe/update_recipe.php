<?php
session_start();
include '../database.php';
include '../navbar.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $title = $_POST['title'];
    $category = $_POST['category'];
    $ingredients = $_POST['ingredients'];
    $steps = $_POST['steps'];

    // Handle image upload and deletion of old image
    if (!empty($_FILES['image']['name'])) {
        // Get the old image name from the database
        $result = $conn->query("SELECT image FROM recipes WHERE id = $id");
        $row = $result->fetch_assoc();
        $oldImage = $row['image'];

        // Delete the old image if exists
        if (!empty($oldImage) && file_exists("uploads/$oldImage")) {
            unlink("uploads/$oldImage");
        }

        // Upload new image
        $target_dir = "uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_sql = ", image = '$image_name'";
        } else {
            die("Error uploading image.");
        }
    } else {
        $image_sql = ""; // No new image, so no image update
    }

    // Prepare the SQL statement
    $sql = "UPDATE recipes SET title = ?, category = ?, ingredients = ?, steps = ? $image_sql WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $title, $category, $ingredients, $steps, $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Recipe updated successfully!';
        header("Location: ../index.php?message=updated");
        exit;
    } else {
        die("Error updating recipe: " . $conn->error);
    }
} else {
    die("Invalid request.");
}
?>
