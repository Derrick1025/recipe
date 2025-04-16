<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base URL if needed (optional for easy path management)
$basePath = '/Recipe_Module/'; // Change this to '/YourFolderName/' if you're running in a subfolder
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= $basePath ?>index.php">Recipe App</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?= $basePath ?>index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $basePath ?>recipe/add_recipe.php">Add Recipe</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $basePath ?>logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
