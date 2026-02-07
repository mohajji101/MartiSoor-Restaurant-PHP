<?php
// views/admin/orders_status.php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($order_id && $status) {
        $pdo = get_db_connection();
        // Update order status
        // Cusbooneysii xaaladda dalabka
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
        $_SESSION['flash_message'] = "Order status updated to $status!";
    }
}

header('Location: /admin/orders');
exit;
