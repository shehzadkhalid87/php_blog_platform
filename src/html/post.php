<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../includes/db.php';
session_start();

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
  die("Invalid post ID.");
}

$val = $db->prepare("
  SELECT posts.*, users.name 
  FROM posts 
  JOIN users ON posts.user_id = users.id 
  WHERE posts.id = ?
");

$val->execute([$id]);

$result = $val->get_result();
$post = $result->fetch_assoc();

if (!$post) {
  die("Post not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($post['title']) ?> - MyBlog</title>
  <link rel="stylesheet" href="../css/post.css">
   <link rel="stylesheet" href="../css/index.css">
</head>
<body>

  <!-- Header -->
  <header class="header">
    <div class="logo">MyBlog</div>
    <nav class="menu">
      <a href="index.php">Home</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="signup.php">Sign Up</a>
      <?php endif; ?>
    </nav>
  </header>

  <!-- Post Content -->
  <main class="post-container">
    <h1 class="post-title"><?= htmlspecialchars($post['title']) ?></h1>

    <?php if ($post['image']): ?>
      <img src="../<?= $post['image'] ?>" class="post-image" alt="Post Image">
    <?php endif; ?>

    <div class="post-meta">
      👤 <strong><?= htmlspecialchars($post['name']) ?></strong> |
      📅 <?= date('F j, Y', strtotime($post['created_at'])) ?>
    </div>

    <p class="post-content">
      <?= nl2br(htmlspecialchars($post['content'])) ?>
    </p>

    <div class="back-link">
      <a href="index.php">← Back to Home</a>
    </div>
  </main>

 <footer class="site-footer">
    &copy; <?= date('Y') ?> MyBlog. All rights reserved.
</footer>
</body>
</html>
