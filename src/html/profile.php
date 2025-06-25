<?php
include '../includes/auth.php';
include '../includes/db.php';
include '../includes/layouts/header.php';
include '../includes/layouts/sidebar.php';

$userId = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = trim($_POST['name']);
    $newUsername = trim($_POST['username']);
    $newPassword = $_POST['password'];

    // Check if username is already taken (by another user)
    $stmt = $db->prepare("SELECT COUNT(*) AS count FROM users WHERE username = ? AND id != ?");
    $stmt->bind_param("si", $newUsername, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        $message = "<p class='error-message'>Username already in use.</p>";
    } else {
        // Update name and username
        $update = $db->prepare("UPDATE users SET name = ?, username = ? WHERE id = ?");
        $update->bind_param("ssi", $newName, $newUsername, $userId);
        $update->execute();

        // Update password if provided
        if (!empty($newPassword)) {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $passUpdate = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $passUpdate->bind_param("si", $hashed, $userId);
            $passUpdate->execute();
        }

        $message = "<p class='success-message'>Profile updated successfully.</p>";
    }
}

// Fetch current user data
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<link rel="stylesheet" href="../css/profile.css">
 <link rel="stylesheet" href="../css/index.css">

<main class="profile-container">
  
<h1 class="profile-title">Edit Profile</h1>

  <?php if (!empty($message)) echo $message; ?>

  <form method="POST" class="profile-form" id="profileForm">
      <div class="form-group">
          <label>Name</label>
          <input name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
      </div>

      <div class="form-group">
          <label>Username</label>
          <input name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
      </div>

      <div class="form-group">
          <label>New Password (leave blank to keep current)</label>
          <input type="password" name="password">
      </div>

      <button type="submit" class="save-btn">Save Changes</button>
  </form>
</main>

<script>
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        const name = this.name.value.trim();
        const username = this.username.value.trim();

        if (name === '' || username === '') {
            e.preventDefault();
            alert('Name and Username cannot be empty.');
        }
    });
</script>
 <footer class="site-footer">
    &copy; <?= date('Y') ?> MyBlog. All rights reserved.
</footer>
