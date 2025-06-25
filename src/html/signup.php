<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $name = $_POST['name'];
    $password = $_POST['password'];

    // Check if username exists
    $check = $db->prepare("SELECT COUNT(*) AS count FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        $error = "Username already taken.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (name, username, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $username, $hashed);
        $stmt->execute();

        $_SESSION['user_id'] = $db->insert_id;
        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Sign Up</title>
  <link rel="stylesheet" href="../css/signup.css">
</head>
<body class="container">
  <div class="form-container">
    <h2 class="title">Register Your Account</h2>

    <?php if (!empty($error)): ?>
      <div class="error">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="form">

        <div class="form-group">
        <label>Name</label>
        <input name="name" required>
      </div>

      <div class="form-group">
        <label>Username</label>
        <input name="username" required>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input name="email address" required>
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>


      <button type="submit" class="submit-btn">Create Account</button>
    </form>

    <p class="login-link">
      Already have an account? <a href="login.php">Login</a>
    </p>
  </div>
</body>
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        const name = this.name.value.trim();
        const username = this.username.value.trim();
         const email = this.email.value.trim();
        const password = this.password.value.trim();

        if (name === '' || username === ''|| email === '' || password === '') {
            e.preventDefault();
            alert('Please fill in all fields.');
        }
    });
</script>
</html>
