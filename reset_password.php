<?php
include 'database.php';
session_start();

$token = $_GET['token'] ?? '';
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($newPassword !== $confirm) {
        $message = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
            $stmt->bind_param("ss", $hashed, $token);
            $stmt->execute();
            $message = "Password has been reset. <a href='login.php'>Login</a>";
        } else {
            $message = "Invalid or expired token.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
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
        <h3 class="text-center mb-4"><i class="bi bi-shield-lock"></i> Reset Password</h3>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <div class="input-group">
                    <input type="password" name="new_password" class="form-control" id="new_password" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('new_password', this)"><i class="bi bi-eye-slash"></i></button>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <div class="input-group">
                    <input type="password" name="confirm_password" class="form-control" id="confirm_password" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirm_password', this)"><i class="bi bi-eye-slash"></i></button>
                </div>
            </div>
            <button type="submit" class="btn btn-success w-100">Reset Password</button>
        </form>
    </div>
</div>
<script>
    function togglePassword(id, btn) {
        const input = document.getElementById(id);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>