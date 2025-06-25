<?php
require '../includes/db.php';
session_start();

// Search & fetch
$search = $_GET['search'] ?? '';

// Fetch posts
$stmt = $db->prepare("
    SELECT posts.*, users.name 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    WHERE posts.title LIKE ? 
    ORDER BY posts.created_at DESC
");

if ($stmt->execute(["%$search%"])) {
    $result = $stmt->get_result();
    $posts = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $posts = []; 
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyBlog - Home</title>
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>

<!-- Header -->
<header class="site-header">
    <div class="logo">MyBlog</div>
    <nav class="navbar">
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

<!-- Hero Section -->
<section class="hero">
    <h1>Welcome to MyBlog</h1>
    <p>A clean, simple blogging platform where you can read and share ideas freely. Built with PHP & Pure CSS.</p>
    <a href="signup.php" class="btn">Get Started</a>
</section>

<!-- Features Section -->
<section class="features">
    <h2>💡 Why MyBlog?</h2>
    <div class="feature-grid">
        <div class="feature-card">
            <h3>Write Freely</h3>
            <p>Share your thoughts and stories with a clean editor and minimal distractions.</p>
        </div>
        <div class="feature-card">
            <h3>Explore Ideas</h3>
            <p>Discover content from others and learn from their experiences and knowledge.</p>
        </div>
        <div class="feature-card">
            <h3>Simple & Fast</h3>
            <p>Powered by PHP — it loads fast and works everywhere.</p>
        </div>
    </div>
</section>

<!-- Search Form -->
<section class="search-section">
    <form method="GET" class="search-form">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search blog posts..." autocomplete="off" id="searchInput">
        <button type="submit">Search</button>
    </form>
</section>

<!-- Posts Grid -->
<main class="posts-section">
    <h2>📰 Latest Posts</h2>

    <div class="posts-grid">
        <?php foreach ($posts as $post): ?>
            <div class="post-card">
                <h3>
                    <a href="post.php?id=<?= $post['id'] ?>">
                        <?= htmlspecialchars($post['title']) ?>
                    </a>
                </h3>
                <?php if ($post['image']): ?>
                    <img src="../<?= $post['image'] ?>" alt="Post Image">
                <?php endif; ?>
                <p><?= htmlspecialchars(substr($post['content'], 0, 120)) ?>...</p>
                <div class="post-meta">
                    👤 <strong><?= htmlspecialchars($post['name']) ?></strong> |
                    📅 <?= date('F j, Y', strtotime($post['created_at'])) ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (count($posts) === 0): ?>
            <p class="no-posts">No posts found.</p>
        <?php endif; ?>
    </div>
</main>

<!-- Footer -->
<footer class="site-footer">
    &copy; <?= date('Y') ?> MyBlog. All rights reserved.
</footer>

<script>
  
    window.onload = function() {
    document.getElementById('searchInput').focus();
};

</script>
</body>
</html>
