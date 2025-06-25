<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $val = $db->prepare("SELECT * FROM users WHERE username = ?");
    $val->bind_param("s", $username);
    $val->execute();
    $result = $val->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="../css/login.css">
</head>
<body class="container">
  <div class="form-container">
    <h2 class="title">🔐 Login</h2>

    <?php if (!empty($error)): ?>
      <div class="error">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="form" id="loginForm">
      <div class="form-group">
        <label>Username</label>
        <input name="username" required>
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>

      <button type="submit" class="submit-btn">Login</button>
    </form>

    <p class="login-link">
      Don’t have an account? <a href="signup.php">Sign up</a>
    </p>
  </div>

  <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      const username = this.username.value.trim();
      const password = this.password.value.trim();

      if (username === '' || password === '') {
        e.preventDefault();
        alert('Please fill in all fields.');
      }
    });
  </script>
</body>
</html>
