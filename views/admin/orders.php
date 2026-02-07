<?php
// views/admin/orders.php

// Check admin role
// Hubi inuu yahay maamule
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /login');
    exit;
}

include __DIR__ . '/../partials/header.php';

$pdo = get_db_connection();

// Status Filter Logic
// Kala soocida dalabyada (Filter)
$status_filter = $_GET['status'] ?? null;
if ($status_filter) {
    // Filter by status
    // Ku kala sooc xaaladda (status)
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE status = ? ORDER BY created_at DESC");
    $stmt->execute([$status_filter]);
} else {
    // Get all orders
    // Soo qaado dhammaan dalabyada
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
}
$orders = $stmt->fetchAll();
?>

<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-12">
            <div>
                <div class="flex items-center gap-4 mb-2">
                    <a href="/admin" class="text-slate-400 hover:text-orange-600 transition"><i
                            class="fas fa-chevron-left"></i></a>
                    <h1 class="text-4xl font-bold text-slate-900">Order Management</h1>
                </div>
                <p class="text-slate-500">Track and manage customer orders</p>
            </div>

            <div class="flex flex-wrap gap-2 mt-6 md:mt-0">
                <a href="/admin/orders"
                    class="px-4 py-2 rounded-xl text-sm font-bold <?php echo !$status_filter ? 'bg-orange-600 text-white' : 'bg-white text-slate-600 border border-slate-200'; ?>">All</a>
                <a href="/admin/orders?status=Pending"
                    class="px-4 py-2 rounded-xl text-sm font-bold <?php echo $status_filter === 'Pending' ? 'bg-orange-600 text-white' : 'bg-white text-slate-600 border border-slate-200'; ?>">Pending</a>
                <a href="/admin/orders?status=Confirmed"
                    class="px-4 py-2 rounded-xl text-sm font-bold <?php echo $status_filter === 'Confirmed' ? 'bg-orange-600 text-white' : 'bg-white text-slate-600 border border-slate-200'; ?>">Confirmed</a>
                <a href="/admin/orders?status=Processing"
                    class="px-4 py-2 rounded-xl text-sm font-bold <?php echo $status_filter === 'Processing' ? 'bg-orange-600 text-white' : 'bg-white text-slate-600 border border-slate-200'; ?>">Processing</a>
                <a href="/admin/orders?status=Out for Delivery"
                    class="px-4 py-2 rounded-xl text-sm font-bold <?php echo $status_filter === 'Out for Delivery' ? 'bg-orange-600 text-white' : 'bg-white text-slate-600 border border-slate-200'; ?>">Delivery</a>
                <a href="/admin/orders?status=Completed"
                    class="px-4 py-2 rounded-xl text-sm font-bold <?php echo $status_filter === 'Completed' ? 'bg-orange-600 text-white' : 'bg-white text-slate-600 border border-slate-200'; ?>">Completed</a>
                <a href="/admin/orders?status=Cancelled"
                    class="px-4 py-2 rounded-xl text-sm font-bold <?php echo $status_filter === 'Cancelled' ? 'bg-orange-600 text-white' : 'bg-white text-slate-600 border border-slate-200'; ?>">Cancelled</a>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-8 py-6">Order ID</th>
                            <th class="px-8 py-6">Customer</th>
                            <th class="px-8 py-6">Total</th>
                            <th class="px-8 py-6">Status</th>
                            <th class="px-8 py-6">Date</th>
                            <th class="px-8 py-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-6">
                                    <span
                                        class="font-bold text-slate-900">#ORD-<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="font-bold text-slate-900">
                                        <?php echo htmlspecialchars($order['user_name']); ?></div>
                                    <div class="text-xs text-slate-500">
                                        <?php echo htmlspecialchars($order['user_email']); ?></div>
                                </td>
                                <td class="px-8 py-6 font-bold text-slate-900">
                                    $<?php echo number_format($order['total'], 2); ?></td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase <?php
                                    echo match ($order['status']) {
                                        'Completed' => 'bg-green-100 text-green-700',
                                        'Pending' => 'bg-orange-100 text-orange-700',
                                        'Cancelled' => 'bg-red-100 text-red-700',
                                        'Confirmed' => 'bg-blue-100 text-blue-700',
                                        'Processing' => 'bg-purple-100 text-purple-700',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                    ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-slate-500 text-sm">
                                    <?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end items-center gap-4">
                                        <form action="/admin/orders/status" method="POST"
                                            class="inline-flex items-center gap-2">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="status" onchange="this.form.submit()"
                                                class="text-xs font-bold bg-slate-50 border border-slate-200 rounded-lg px-2 py-1 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                                <option value="Pending" <?php echo $order['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="Confirmed" <?php echo $order['status'] === 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                <option value="Processing" <?php echo $order['status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="Out for Delivery" <?php echo $order['status'] === 'Out for Delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                                                <option value="Completed" <?php echo $order['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                                <option value="Cancelled" <?php echo $order['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                        </form>
                                        <a href="/orders/details?id=<?php echo $order['id']; ?>"
                                            class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-900 hover:text-white transition">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="6" class="px-8 py-10 text-center text-slate-400 font-medium italic">No orders
                                    found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>