<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include '../includes/auth.php';
include '../includes/db.php';
include '../includes/layouts/header.php';
include '../includes/layouts/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imagePath = null;

    if (!empty($_FILES['image']['name'])) {
        $uploadDir = '../uploads/images/';
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = 'uploads/' . $imageName;
        }
    }

    $stmt = $db->prepare("INSERT INTO posts (title, content, image, user_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $content, $imagePath, $_SESSION['user_id']]);

    header("Location: dashboard.php");
    exit;
}
?>

<link rel="stylesheet" href="../css/create_post.css">
 <link rel="stylesheet" href="../css/index.css">

<main class="create-container">
    <h1 class="create-title">Create New Post</h1>

    <form method="POST" enctype="multipart/form-data" class="create-form">
        <div class="form-group">
            <label>Title</label>
            <input name="title" required>
        </div>

        <div class="form-group">
            <label>Content</label>
            <textarea name="content" rows="6" required></textarea>
        </div>

        <div class="form-group">
            <label>Post Image</label>
            <input type="file" name="image" accept="image/*" onchange="previewImage(event)">
            <img id="preview" class="image-preview" />
        </div>

        <button type="submit" class="submit-btn">Publish Post</button>
    </form>
</main>
 <footer class="site-footer">
    &copy; <?= date('Y') ?> MyBlog. All rights reserved.
</footer>

<script>
  function previewImage(event) {
    const preview = document.getElementById('preview');
    const file = event.target.files[0];
    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
}

</script>

