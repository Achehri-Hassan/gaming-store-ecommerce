<?php
http_response_code(404);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'src/helpers/helpers.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found</title>
    <link rel="stylesheet" href="css/components/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .error-page { min-height: 60vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 40px 16px; }
        .error-code  { font-size: 7rem; font-weight: 900; color: #3b82f6; line-height: 1; }
        .error-title { color: #e5e7eb; font-size: 1.8rem; margin: 12px 0 8px; }
        .error-desc  { color: #9ca3af; margin-bottom: 28px; }
        .error-btn   { background: #3b82f6; color: #fff; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-size: 1rem; }
    </style>
</head>
<body>
<?php include 'src/views/layouts/header.php'; ?>
<main class="error-page">
    <div>
        <div class="error-code">404</div>
        <h1 class="error-title">Page Not Found</h1>
        <p class="error-desc">The page you're looking for doesn't exist or has been moved.</p>
        <a href="index.php" class="error-btn"><i class="fas fa-home"></i> Back to Home</a>
    </div>
</main>
<?php include 'src/views/layouts/footer.php'; ?>
</body>
</html>
