<?php
include 'database.php';
session_start();

$message = "";
$showResetLink = false;
$resetLink = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $token = bin2hex(random_bytes(32));
        $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        $resetLink = "reset_password.php?token=$token";
        $showResetLink = true;
    } else {
        $message = "Email not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            background: linear-gradient(135deg, #c3ecf4, #a0c3ff);
        }
        .card {
            background: #ffffffee; /* soft white with transparency */
            border-radius: 12px;
            animation: fadeSlide 0.4s ease-in-out;
        }
        @keyframes fadeSlide {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-light">
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-lg" style="max-width: 420px; width: 100%;">
        <h3 class="text-center mb-4"><i class="bi bi-question-circle"></i> Forgot Password</h3>
        <?php if ($message): ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-floating mb-3">
                <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
                <label for="email">Email</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Generate Reset Link</button>
        </form>
        <p class="mt-3 text-center"><a href="login.php">Back to login</a></p>
        <?php if ($showResetLink): ?>
            <div class="mt-3 alert alert-info">
                <strong>Reset Link:</strong><br>
                <a href="<?php echo $resetLink; ?>"><?php echo $resetLink; ?></a>
            </div>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>