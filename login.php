<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerate session ID on each new visit to prevent fixation
if (empty($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

require_once 'src/models/UserModel.php';
require_once 'src/helpers/helpers.php';

// Already logged in — redirect away
if (!empty($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'src/views/admin/admin_dashboard.php' : 'index.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $user = loginUser($email);

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true); // prevent session fixation after login

            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: src/views/admin/admin_dashboard.php');
            } elseif (!empty($_SESSION['redirect_to'])) {
                $loc = $_SESSION['redirect_to'];
                unset($_SESSION['redirect_to']);
                header("Location: $loc");
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            // Generic message — do not disclose whether email exists
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'All fields are required.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Gaming Store</title>
    <link rel="stylesheet" href="css/form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>
<body>
<main>
    <section class="contact-container">
        <div class="contact__image">
            <img src="src/assets/banners_hero_section/pexels-yankrukov-9072299.jpg" alt="Gaming">
            <h2 class="image-overlay-text">Welcome Back</h2>
        </div>

        <div class="contact__form-section" style="margin-top:50px">
            <div class="form__header">
                <h2 class="form__title">Sign In</h2>
            </div>

            <?php if ($error): ?>
                <div class="alert alert--error"><?= h($error) ?></div>
            <?php endif; ?>

            <form class="form__body" method="POST" action="login.php">
                <?= csrf_field() ?>

                <div class="form__group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           placeholder="Enter your email"
                           value="<?= h($_POST['email'] ?? '') ?>"
                           required autocomplete="email">
                </div>

                <div class="form__group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                           placeholder="Enter your password"
                           required autocomplete="current-password">
                </div>

                <div class="form__group">
                    <button type="submit" class="form__button">Login</button>
                </div>

                <div class="don_have_account">
                    <p>Don't have an account? <a href="register.php">Create Account</a></p>
                </div>
            </form>
        </div>
    </section>
</main>
</body>
</html>
