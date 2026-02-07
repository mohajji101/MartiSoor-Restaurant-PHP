<?php
// views/admin/categories/delete.php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login');
    exit;
}

$id = $_GET['id'] ?? null;
if ($id) {
    $pdo = get_db_connection();
    // Delete category
    // Tir qaybta
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: /PHP/Projects/Restaurant-Ordering-System/public/admin/categories');
exit;
