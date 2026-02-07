<?php
// views/order_details.php
if (!isset($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

$pdo = get_db_connection();
$order_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user']['id'];

if (!$order_id) {
    header('Location: /orders');
    exit;
}

// Fetch order info
// Soo qaado macluumaadka dalabka
if ($_SESSION['user']['role'] === 'admin') {
    // Admin can view any order
    // Maamuluhu wuxuu arki karaa dalab kasta
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
} else {
    // Customers can only view their own orders
    // Macmiilku wuxuu arki karaa oo kaliya dalabkiisa
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
}
$order = $stmt->fetch();

if (!$order) {
    // If not found, redirect appropriately
    // Haddii aan la helin, dib u jiheey
    if ($_SESSION['user']['role'] === 'admin') {
        header('Location: /admin/orders');
    } else {
        header('Location: /orders');
    }
    exit;
}

// Fetch order items
// Soo qaado alaabta dalabka
$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

include __DIR__ . '/partials/header.php';
?>

<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12 flex items-center justify-between">
            <div>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <a href="/admin/orders" class="text-slate-400 hover:text-orange-600 transition mb-4 inline-block"><i
                            class="fas fa-arrow-left mr-2"></i> Back to Dashboard</a>
                <?php else: ?>
                    <a href="/orders" class="text-slate-400 hover:text-orange-600 transition mb-4 inline-block"><i
                            class="fas fa-arrow-left mr-2"></i> Back to History</a>
                <?php endif; ?>
                <h1 class="text-4xl font-bold text-slate-900">Order Details</h1>
                <p class="text-slate-500">Order #ORD-<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></p>
            </div>
            <div class="text-right">
                <span class="inline-flex px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest <?php
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <!-- Items list -->
                <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden">
                    <div class="p-8 border-b border-slate-50">
                        <h3 class="text-xl font-bold text-slate-900">Ordered Items</h3>
                    </div>
                    <div class="divide-y divide-slate-50">
                        <?php foreach ($items as $item): ?>
                            <div class="p-8 flex items-center gap-6">
                                <div class="w-16 h-16 bg-slate-50 rounded-xl overflow-hidden flex-shrink-0">
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>"
                                        class="w-full h-full object-cover" alt="">
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-slate-900"><?php echo htmlspecialchars($item['title']); ?>
                                    </h4>
                                    <p class="text-sm text-slate-500">Quantity: <?php echo $item['quantity']; ?></p>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="font-bold text-slate-900">$<?php echo number_format($item['line_total'], 2); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <!-- Summary -->
                <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 p-8">
                    <h3 class="text-xl font-bold text-slate-900 mb-6 pb-4 border-b border-slate-50">Summary</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal</span>
                            <span class="font-bold">$<?php echo number_format($order['subtotal'], 2); ?></span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Delivery Fee</span>
                            <span class="font-bold">$<?php echo number_format($order['delivery_fee'], 2); ?></span>
                        </div>
                        <div class="pt-4 border-t border-slate-50 flex justify-between items-center text-slate-900">
                            <span class="font-bold text-lg">Total</span>
                            <span
                                class="font-black text-2xl text-orange-600">$<?php echo number_format($order['total'], 2); ?></span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900 rounded-[2rem] shadow-xl p-8 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-lg font-bold mb-4">Payment Info</h3>
                        <p class="text-slate-400 text-sm mb-2">Method: <span class="text-white font-medium">Cash on
                                Delivery</span></p>
                        <p class="text-slate-400 text-sm italic">Payment will be collected at the door.</p>
                    </div>
                    <i
                        class="fas fa-truck absolute -bottom-4 -right-4 text-slate-800 text-8xl opacity-30 transform -rotate-12"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>