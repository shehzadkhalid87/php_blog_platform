<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../includes/auth.php';
include '../includes/db.php';
include '../includes/layouts/header.php';
include '../includes/layouts/sidebar.php';

// Get counts
$totalUsersStmt = $db->query("SELECT COUNT(*) AS count FROM users");
$totalUsersRow = $totalUsersStmt->fetch_assoc();
$totalUsers = $totalUsersRow['count'];

$totalPostsStmt = $db->query("SELECT COUNT(*) AS count FROM posts");
$totalPostsRow = $totalPostsStmt->fetch_assoc();
$totalPosts = $totalPostsRow['count'];

// recent posts
$recentStmt = $db->prepare("
    SELECT posts.*, users.name 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    ORDER BY posts.created_at DESC 
    LIMIT 5
");
$recentStmt->execute();
$result = $recentStmt->get_result();
$recentPosts = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="../css/dashboard.css">

</head>

<body>
  <div class="page-wrapper">
    <main class="dashboard">
      <h1 class="dashboard-title">Dashboard Overview</h1>

     
      <div class="summary-cards">
        <div class="summary-card">
          <div class="summary-number"><?= $totalUsers ?></div>
          <div class="summary-label">Total Users</div>
        </div>

        <div class="summary-card">
          <div class="summary-number"><?= $totalPosts ?></div>
          <div class="summary-label">Total Posts</div>
        </div>
      </div>

      <!-- Recent Posts -->
      <div class="recent-posts">
        <h2 class="recent-title">Recent Posts</h2>

        <ul class="post-list">
          <?php foreach ($recentPosts as $post): ?>
            <li class="post-item">
              <div class="post-content">
                <div>
                  <p class="post-title">
                    <a href="post.php?id=<?= $post['id'] ?>" class="read-more-link">
                      <?= htmlspecialchars($post['title']) ?>
                    </a>
                  </p>
                  <p class="post-meta">
                    By <?= htmlspecialchars($post['name']) ?> on <?= date('M d, Y', strtotime($post['created_at'])) ?>
                  </p>
                  <a href="post.php?id=<?= $post['id'] ?>" class="read-more-button">Read More →</a>
                </div>

                <?php if ($post['image']): ?>
                  <img src="../<?= $post['image'] ?>" class="post-thumbnail" alt="Thumbnail">
                <?php endif; ?>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>


        <?php if (empty($recentPosts)): ?>
          <p class="no-posts">No recent posts found.</p>
        <?php endif; ?>
      </div>

    </main>
<footer class="site-footer">
    &copy; <?= date('Y') ?> MyBlog. All rights reserved.
</footer>
</body>

</html>