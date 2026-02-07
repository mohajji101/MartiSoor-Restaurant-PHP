<?php
// views/orders.php
if (!isset($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

$pdo = get_db_connection();
$user_id = $_SESSION['user']['id'];

// Get user orders
// Soo qaado dalabyada isticmaalaha
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

include __DIR__ . '/partials/header.php';
?>

<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <h1 class="text-4xl font-bold text-slate-900 mb-2">Order History</h1>
            <p class="text-slate-500">Track and view your previous orders</p>
        </div>

        <?php if (empty($orders)): ?>
            <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 p-16 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-receipt text-3xl text-slate-300"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">No orders found</h3>
                <p class="text-slate-500 mb-8">You haven't placed any orders yet. Ready to try something delicious?</p>
                <a href="/products"
                    class="bg-orange-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-orange-700 transition shadow-lg shadow-orange-200">
                    Explore Menu
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($orders as $order): ?>
                    <div
                        class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden hover:border-orange-200 transition group">
                        <div class="p-8">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                                <div>
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 block">Order
                                        ID</span>
                                    <h3 class="text-lg font-bold text-slate-900">
                                        #ORD-<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></h3>
                                </div>
                                <div class="flex items-center gap-6">
                                    <div class="text-right">
                                        <span
                                            class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 block">Date</span>
                                        <span
                                            class="text-slate-700 font-medium"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 block">Status</span>
                                        <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-bold uppercase <?php
                                        echo match ($order['status']) {
                                            'Completed' => 'bg-green-100 text-green-700',
                                            'Pending' => 'bg-orange-100 text-orange-700',
                                            'Cancelled' => 'bg-red-100 text-red-700',
                                            'Confirmed' => 'bg-blue-100 text-blue-700',
                                            'Processing' => 'bg-purple-100 text-purple-700',
                                            default => 'bg-slate-100 text-slate-700'
                                        };
                                        ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-50 rounded-2xl p-6 mb-6">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600 font-medium">Order Total</span>
                                    <span
                                        class="text-2xl font-black text-slate-900">$<?php echo number_format($order['total'], 2); ?></span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center">
                                <a href="/orders/details?id=<?php echo $order['id']; ?>"
                                    class="text-slate-500 hover:text-orange-600 font-bold text-sm flex items-center gap-2 transition">
                                    <i class="fas fa-eye"></i>
                                    View Details
                                </a>
                                <span class="text-xs text-slate-400 font-medium italic">Payment: Cash on Delivery</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>