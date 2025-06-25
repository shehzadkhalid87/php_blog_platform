<?php
include '../includes/auth.php';
include '../includes/db.php';
include '../includes/layouts/header.php';
include '../includes/layouts/sidebar.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo "<main class='container'><p class='error'>Invalid post ID.</p></main>";
    exit;
}

$stmt = $db->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    echo "<main class='container'><p class='error'>Post not found or access denied.</p></main>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imagePath = $post['image'];

    if (!empty($_FILES['image']['name'])) {
        $uploadDir = '../uploads/';
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = 'uploads/' . $imageName;
        }
    }

    $updateStmt = $db->prepare("UPDATE posts SET title = ?, content = ?, image = ? WHERE id = ? AND user_id = ?");
    $updateStmt->execute([$title, $content, $imagePath, $id, $_SESSION['user_id']]);

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post</title>
    <link rel="stylesheet" href="../css/edit.css">
     <link rel="stylesheet" href="../css/index.css">
</head>
<body>

<main class="container">
    <h1>Edit Post</h1>

    <form method="POST" enctype="multipart/form-data" class="edit-form">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
        </div>

        <div class="form-group">
            <label>Content</label>
            <textarea name="content" rows="6" required><?= htmlspecialchars($post['content']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Current Image</label>
            <?php if ($post['image']): ?>
                <img src="../<?= htmlspecialchars($post['image']) ?>" class="image-preview" id="currentImage">
            <?php else: ?>
                <p>No image uploaded.</p>
            <?php endif; ?>

            <label>Change Image</label>
            <input type="file" name="image" accept="image/*" onchange="previewImage(event)">
            <img id="preview" class="image-preview hidden">
        </div>

        <button type="submit" class="submit-btn">Update Post</button>
    </form>
</main>

<script>
function previewImage(event) {
    const preview = document.getElementById('preview');
    const currentImage = document.getElementById('currentImage');
    const file = event.target.files[0];

    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
        if (currentImage) {
            currentImage.style.display = 'none';
        }
    }
}
</script>

 <footer class="site-footer">
    &copy; <?= date('Y') ?> MyBlog. All rights reserved.
</footer>
</body>
</html>