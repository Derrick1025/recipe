<?php
session_start();
include 'database.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get sorting, filtering, and search parameters
$sort = $_GET['sort'] ?? 'title';
$categoryFilter = $_GET['category'] ?? '';
$searchTerm = trim($_GET['search'] ?? '');

// Construct SQL query with search, filter, and sorting
$sql = "SELECT * FROM recipes WHERE 1=1";
if (!empty($categoryFilter)) {
    $sql .= " AND category = '" . $conn->real_escape_string($categoryFilter) . "'";
}
if (!empty($searchTerm)) {
    $sql .= " AND title LIKE '%" . $conn->real_escape_string($searchTerm) . "%'";
}
$sql .= " ORDER BY " . ($sort === 'title' ? "title ASC" : "id DESC");

$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Get unique categories for filtering
$categoryQuery = "SELECT DISTINCT category FROM recipes";
$categoryResult = $conn->query($categoryQuery);
$categories = [];
while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row['category'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recipe List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            background: linear-gradient(135deg, #c3ecf4, #a0c3ff);
            overflow-x: hidden;
        }
    </style>
</head>

<body class="bg-light">
<div class="container mt-4">
    <h2 class="mb-4">Recipe List</h2>

    <!-- Success Message -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?= $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- Filter & Search -->
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category ?>" <?= ($category === $categoryFilter) ? 'selected' : '' ?>><?= $category ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="title" <?= ($sort === 'title') ? 'selected' : '' ?>>Sort A-Z</option>
                <option value="date" <?= ($sort === 'date') ? 'selected' : '' ?>>Newest First</option>
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" class="form-control" placeholder="Search recipe title...">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Apply</button>
        </div>
    </form>


        <div class="row">
            <?php if ($result->num_rows > 0) { ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <?php if (!empty($row['image'])) { ?>
                                <img src="recipe/uploads/<?php echo $row['image']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <?php } ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row['title']; ?></h5>
                                <p class="card-text"><strong>Category:</strong> <?php echo $row['category']; ?></p>

                                <!-- View button -->
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal"
                                    data-title="<?php echo $row['title']; ?>"
                                    data-category="<?php echo $row['category']; ?>"
                                    data-ingredients="<?php echo htmlspecialchars($row['ingredients']); ?>"
                                    data-steps="<?php echo htmlspecialchars($row['steps']); ?>"
                                    data-image="<?php echo $row['image']; ?>"
                                    data-id="<?php echo $row['id']; ?>">
                                    <i class="bi bi-eye"></i> View
                                </button>

                                <?php if ($_SESSION['role'] === 'admin' || $row['user_id'] == $_SESSION['user_id']) { ?>
                                    <!-- Edit Button -->
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-title="<?php echo $row['title']; ?>"
                                        data-category="<?php echo $row['category']; ?>"
                                        data-ingredients="<?php echo htmlspecialchars($row['ingredients']); ?>"
                                        data-steps="<?php echo htmlspecialchars($row['steps']); ?>"
                                        data-image="<?php echo $row['image']; ?>">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>

                                    <!-- Delete Button -->
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        data-id="<?php echo $row['id']; ?>" data-title="<?php echo $row['title']; ?>">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p>No recipes found.</p>
            <?php } ?>
        </div>
    </div>

    <!-- View Recipe Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">View Recipe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4 id="viewTitle"></h4>
                    <p><strong>Category:</strong> <span id="viewCategory"></span></p>
                    <h5>Ingredients</h5>
                    <p id="viewIngredients"></p>
                    <h5>Steps</h5>
                    <p id="viewSteps"></p>
                    <img src="" id="viewImage" class="img-fluid" alt="Recipe Image">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Recipe Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Recipe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="recipe/update_recipe.php" enctype="multipart/form-data">
                        <input type="hidden" id="editRecipeId" name="id">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" id="editTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ingredients</label>
                            <textarea class="form-control" id="editIngredients" name="ingredients" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Steps</label>
                            <textarea class="form-control" id="editSteps" name="steps" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-control" id="editCategory" required>
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
                            <input type="file" class="form-control" name="image">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the recipe <strong id="recipeTitle"></strong>? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle the 'View' button click event
        var viewModal = document.getElementById('viewModal');
        viewModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            document.getElementById('viewTitle').textContent = button.getAttribute('data-title');
            document.getElementById('viewCategory').textContent = button.getAttribute('data-category');
            document.getElementById('viewIngredients').textContent = button.getAttribute('data-ingredients');
            document.getElementById('viewSteps').innerHTML = nl2br(button.getAttribute('data-steps'));
            document.getElementById('viewImage').src = "recipe/uploads/" + button.getAttribute('data-image');

            // Image display logic
            var imageName = button.getAttribute('data-image');
            var imageElement = document.getElementById('viewImage');

            if (imageName && imageName.trim() !== '') {
                imageElement.src = "recipe/uploads/" + imageName;
                imageElement.style.display = "block";
                imageElement.alt = "Recipe Image";
            } else {
                imageElement.src = "";
                imageElement.style.display = "none";
            }
        });

        // Handle the 'Edit' button click event
        var editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            document.getElementById('editRecipeId').value = button.getAttribute('data-id');
            document.getElementById('editTitle').value = button.getAttribute('data-title');
            document.getElementById('editCategory').value = button.getAttribute('data-category');
            document.getElementById('editIngredients').value = button.getAttribute('data-ingredients');
            document.getElementById('editSteps').value = button.getAttribute('data-steps');
        });

        // Handle the 'Delete' button click event
        var deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            document.getElementById('recipeTitle').textContent = button.getAttribute('data-title');
            document.getElementById('confirmDelete').href = "recipe/delete_recipe.php?id=" + button.getAttribute('data-id');
        });

        // Helper function to convert newline characters to <br> tags
        function nl2br(str) {
            return str.replace(/\n/g, "<br>");
        }

        // Automatically hide the success message after 5 seconds
        setTimeout(function() {
            var successMessage = document.querySelector('.alert-success');
            if (successMessage) {
                successMessage.classList.add('fade');
                successMessage.classList.remove('show');
                // After fading out, remove the element from the DOM to avoid blank space
                setTimeout(function() {
                    successMessage.remove();
                }, 500); // This time should match the duration of the fade-out transition
            }
        }, 5000); // 5000ms = 5 seconds
    </script>
</body>

</html>