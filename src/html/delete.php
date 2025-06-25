<?php
include '../includes/auth.php';
include '../includes/db.php';

$id = $_GET['id'];
$stmt = $db->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);

header("Location: dashboard.php");
exit;
