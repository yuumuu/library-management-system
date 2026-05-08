<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : 'Library Management' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body id="body">
    <?php if (isset($_SESSION['admin'])): ?>
    <nav style="position: sticky; top: 0; z-index: 1000; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <a href="index.php" class="nav-brand" style="display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 1.5rem;">📚</span>
            <span>LibSystem <span style="color: var(--text-muted); font-weight: 400; font-size: 0.875rem;">Pro</span></span>
        </a>
        <div style="display: flex; gap: 1.5rem; align-items: center;">
            <button class="theme-toggle" onclick="toggleTheme()" title="Toggle Theme" style="background: rgba(0,0,0,0.03); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 10px;">🌓</button>
            <form action="logout.php" method="POST" style="margin: 0;">
                <button type="submit" class="btn btn-danger" style="padding: 0.625rem 1.25rem; font-size: 0.8125rem; border-radius: 10px; font-weight: 700; letter-spacing: 0.025em; text-transform: uppercase;">Logout</button>
            </form>
        </div>
    </nav>
    <?php endif; ?>
    <div class="container">
