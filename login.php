<?php
$page_title = "Admin Login";
require 'header.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === "admin" && $password === "admin123") {
        $_SESSION['admin'] = $username;
        header("Location: index.php");
        exit();
    } else {
        $error = "❌ Invalid username or password.";
    }
}
?>

<div style="max-width: 400px; margin: 4rem auto;">
    <div class="card">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="font-size: 1.5rem; margin-bottom: 0.5rem;">🔐 Admin Login</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Access the Library Management System</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
        </form>
    </div>
    
    <div style="text-align: center; margin-top: 2rem;">
        <button class="theme-toggle" onclick="toggleTheme()" title="Toggle Theme">🌓 Toggle Mode</button>
    </div>
</div>

<?php require 'footer.php'; ?>
