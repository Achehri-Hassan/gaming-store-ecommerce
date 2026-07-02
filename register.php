<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once 'src/config/connection.php';
require_once 'src/models/UserModel.php';
require_once 'src/helpers/helpers.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username         = clean($_POST['name'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required.';
    } elseif (strlen($username) < 2) {
        $error = 'Name must be at least 2 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (emailExists($email)) {
        $error = 'This email is already registered.';
    } else {
        if (createUser($username, $email, $password)) {
            header('Location: login.php');
        } else {
            $error = 'Something went wrong. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — Gaming Store</title>
    <link rel="stylesheet" href="css/form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>
<body>
<main>
    <section class="contact-container">
        <div class="contact__image">
            <img src="src/assets/banners_hero_section/login_woman 1.jpg" alt="Gaming">
            <h2 class="image-overlay-text">Hi There</h2>
        </div>

        <div class="contact__form-section" style="margin-top:25px">
            <div class="form__header">
                <h2 class="form__title">Create Account</h2>
            </div>

            <?php if ($error): ?>
                <div class="alert alert--error"><?= h($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert--success"><?= h($success) ?></div>
            <?php endif; ?>

            <form class="form__body" method="POST" action="register.php">
                <?= csrf_field() ?>

                <div class="form__group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name"
                           placeholder="Enter your name"
                           value="<?= h($_POST['name'] ?? '') ?>"
                           required autocomplete="name">
                </div>

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
                           placeholder="At least 6 characters"
                           required autocomplete="new-password">
                </div>

                <div class="form__group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                           placeholder="Repeat your password"
                           required autocomplete="new-password">
                </div>

                <button type="submit" class="form__button">Create Account</button>

                <div class="don_have_account" style="margin-top:16px">
                    <p>Already have an account? <a href="login.php">Sign In</a></p>
                </div>
            </form>
        </div>
    </section>
</main>
</body>
</html>
