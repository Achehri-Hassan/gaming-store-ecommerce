<?php



require_once 'src/config/connection.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$error = '';
$success = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $username = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
 
  if (!empty($username) && !empty($email) && !empty($password) && !empty($confirm_password)) {

    if ($password !== $confirm_password) {
      $error = "Passwords do not match!";
      
    } else {
      $conn = getConnection();

      $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
      $stmt->execute([':email' => $email]);

      if ($stmt->fetch()) {
        $error = "This email is already registered!";
      } else {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


        $insert = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'user')");

        if ($insert->execute([
          ':username' => $username,
          ':email' => $email,
          ':password' => $hashedPassword
        ])) {
          $success = "Account created successfully! Redirecting to login...";

          header("Refresh: 2; url=login.php");
        } else {
          $error = "Something went wrong! Please try again.";
        }
      }
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
  <title>Document</title>

  <!-- link css design -->
  <link rel="stylesheet" href="css/form.css">

  <!-- link icon  -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
</head>

<body>

  <main>
    <section class="contact-container">
      <div class="contact__image">
        <img src="src/assets/banners_hero_section/login_woman 1.jpg" alt="Support" />
        <h2 class="image-overlay-text">Hi There</h2>
      </div>

      <div class="contact__form-section" style="margin-top: 25px;">
        <div class="form__header">
          <h2 class="form__title">Create Account</h2>
        </div>

        <form class="form__body" method="post">
          <?php if (!empty($error)): ?>
            <div style="background: rgba(255, 77, 77, 0.1); color: #ff4d4d; border: 1px solid #ff4d4d; padding: 12px; border-radius: 6px; text-align: center; margin-bottom: 20px; font-weight: bold;">
              <?= $error ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($success)): ?>
            <div style="background: rgba(139, 251, 2, 0.1); color: #8bfb02; border: 1px solid #8bfb02; padding: 12px; border-radius: 6px; text-align: center; margin-bottom: 20px; font-weight: bold;">
              <?= $success ?>
            </div>
          <?php endif; ?>
          <div class="form__group">
            <label>Full name</label>
            <input type="text" placeholder="Enter your name" name="name" required />
          </div>

          <div class="form__group">
            <label>Email</label>
            <input type="email" placeholder="Enter your email" name="email" required />
          </div>

          <div class="form__group">
            <label>Password</label>
            <input type="password" placeholder="Enter your name" name="password" required />
          </div>

           <div class="form__group">
            <label>Confirm Password</label>
            <input type="password" placeholder="Enter your name" name="confirm_password" required />
          </div>
          
          <button type="submit" class="form__button" name="Register">Submit</button>
        </form>
      </div>
    </section>
  </main>
</body>

</html>