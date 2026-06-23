<?php



require_once 'src/models/UserModel.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}



$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  if (!empty($email) && !empty($password)) {

    $user = loginUser($email);

    if ($user && password_verify($password, $user['password'])) {

      // SESSION
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['role'] = $user['role'];

      // REDIRECT
      if ($user['role'] === 'admin') {
        header("Location: admin_dashboard.php");
        exit;
      }

      if (isset($_SESSION['redirect_to'])) {
        $location = $_SESSION['redirect_to'];
        unset($_SESSION['redirect_to']);
        header("Location: $location");
      } else {
        header("Location: index.php");
      }
      exit;
    } else {
      $error = "Invalid Email or Password!";
    }
  } else {
    $error = "All fields are required!";
  }
}


?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tech Shop - Login</title>

  <link rel="stylesheet" href="css/form.css">

  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
</head>

<body>

  <main>
    <section class="contact-container">
      <div class="contact__image">
        <img src="src/assets/banners_hero_section/pexels-yankrukov-9072299.jpg" alt="Support" />
        <h2 class="image-overlay-text">Welcome Back</h2>
      </div>

      <div class="contact__form-section" style="margin-top: 50px;">
        <div class="form__header">
          <h2 class="form__title">Sign In</h2>
        </div>

        <?php if (!empty($error)): ?>
          <div style="background: rgba(255, 77, 77, 0.1); color: #ff4d4d; border: 1px solid #ff4d4d; padding: 12px; border-radius: 6px; text-align: center; margin-bottom: 20px; font-weight: bold;">
            <?= $error ?>
          </div>
        <?php endif; ?>

        <form class="form__body" method="post">

          <div class="form__group">
            <label>Email</label>
            <input type="email" placeholder="Enter your email" name="email" required />
          </div>

          <div class="form__group">
            <label>Password</label>
            <input type="password" placeholder="Enter your password" name="password" required />
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