<?php
include '../includes/auth.php';
include '../includes/db.php';
include '../includes/layouts/header.php';
include '../includes/layouts/sidebar.php';

// Handle search + pagination
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;
$userId = $_SESSION['user_id'];

// Count total matching posts
$count = $db->prepare("SELECT COUNT(*) AS total FROM posts WHERE user_id = ? AND title LIKE ?");
$count->execute([$userId, "%$search%"]);
$countResult = $count->get_result()->fetch_assoc();

$totalPosts = $countResult ? $countResult['total'] : 0;
$totalPages = $totalPosts > 0 ? ceil($totalPosts / $limit) : 1;

$stmt = $db->prepare("
    SELECT * FROM posts 
    WHERE user_id = ? AND title LIKE ? 
    ORDER BY created_at DESC 
    LIMIT $limit OFFSET $offset
");
$stmt->execute([$userId, "%$search%"]);
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);
?>

<link rel="stylesheet" href="../css/posts.css">
 <link rel="stylesheet" href="../css/index.css">

<main class="container">
    <h1 class="title">Your Posts</h1>

    <form method="GET" class="search-form">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search your posts..." class="search-input">
        <button type="submit" class="search-button">Search</button>
    </form>

    <a href="create.php" class="create-button">➕ New Post</a>


    <div class="posts-list">
        <?php if (count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <div class="post-card">
                    <h2 class="post-title"><?= htmlspecialchars($post['title']) ?></h2>
                    <p class="post-content"><?= htmlspecialchars(substr($post['content'], 0, 100)) ?>...</p>
                    <?php if ($post['image']): ?>
                        <img src="../<?= $post['image'] ?>" class="post-image" alt="Post Image">
                    <?php endif; ?>
                    <div class="post-actions">
                        <a href="edit.php?id=<?= $post['id'] ?>" class="edit-link">Edit</a> |
                        <a href="delete.php?id=<?= $post['id'] ?>" class="delete-link" onclick="return confirm('Are you sure?')">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-posts">No posts found.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?search=<?= urlencode($search) ?>&page=<?= $i ?>" class="page-link <?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</main>

 <footer class="site-footer">
    &copy; <?= date('Y') ?> MyBlog. All rights reserved.
</footer>
