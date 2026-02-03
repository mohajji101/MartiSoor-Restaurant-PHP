<?php
// views/checkout.php
if (!isset($_SESSION['user'])) {
    header('Location: ./login?redirect=checkout');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: ./cart');
    exit;
}

$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$pdo = get_db_connection();
// Fetch current settings, defaulting to 0 if not set, strictly following admin configuration.
$delivery_fee_setting = get_setting($pdo, 'delivery_fee', '0.00'); 
$free_delivery_threshold_setting = get_setting($pdo, 'free_delivery_threshold', '0.00');

$delivery_fee = (float)$delivery_fee_setting;
$free_delivery_threshold = (float)$free_delivery_threshold_setting;

// Calculate shipping
$shipping = ($subtotal >= $free_delivery_threshold) ? 0 : $delivery_fee;
$total = $subtotal + $shipping;

$success = false;
$order_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = get_db_connection();
    
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, user_name, user_email, subtotal, delivery_fee, total, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user']['id'],
            $_SESSION['user']['name'],
            $_SESSION['user']['email'],
            $subtotal,
            $shipping,
            $total,
            'Pending'
        ]);
        
        $order_id = $pdo->lastInsertId();
        
        $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, title, price, quantity, image, line_total) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($cart as $id => $item) {
            $item_stmt->execute([
                $order_id,
                $id,
                $item['title'],
                $item['price'],
                $item['quantity'],
                $item['image'],
                $item['price'] * $item['quantity']
            ]);
        }
        
        $pdo->commit();
        unset($_SESSION['cart']);
        $success = true;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Failed to process order: " . $e->getMessage();
    }
}

include 'partials/header.php';
?>

<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($success): ?>
            <div class="bg-white rounded-[3rem] p-20 text-center shadow-2xl border border-slate-100">
                <div class="w-32 h-32 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-8">
                    <i class="fas fa-check text-5xl text-green-500"></i>
                </div>
                <h2 class="text-4xl font-bold text-slate-900 mb-4">Order Confirmed!</h2>
                <p class="text-slate-500 mb-10 text-lg">Thank you for your order #<?php echo $order_id; ?>. We're currently preparing your delicious meal!</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="./" class="bg-orange-600 text-white px-10 py-4 rounded-full font-bold shadow-xl shadow-orange-200">Track Order</a>
                    <a href="./products" class="bg-slate-100 text-slate-600 px-10 py-4 rounded-full font-bold">Back to Menu</a>
                </div>
            </div>
        <?php else: ?>
            <h1 class="text-4xl font-bold text-slate-900 mb-12">Complete Your Order</h1>
            
            <div class="grid grid-cols-1 gap-12">
                <div class="bg-white p-10 rounded-[2.5rem] shadow-xl border border-slate-100">
                    <h3 class="text-2xl font-bold text-slate-900 mb-8 border-b border-slate-50 pb-6">Shipping Details</h3>
                    <form action="" method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Delivery Address</label>
                                <textarea required class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition h-32" placeholder="Street name, Building, Apartment..."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">City</label>
                                <input type="text" required class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Phone Number</label>
                                <input type="tel" required class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                            </div>
                        </div>
                        
                        <div class="pt-10">
                            <h3 class="text-2xl font-bold text-slate-900 mb-8 border-b border-slate-50 pb-6">Payment Method</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="payment" value="cash" checked class="peer sr-only">
                                    <div class="p-6 border-2 border-slate-100 rounded-2xl peer-checked:border-orange-500 peer-checked:bg-orange-50 transition">
                                        <i class="fas fa-money-bill-wave text-2xl mb-2 text-slate-400 group-peer-checked:text-orange-600"></i>
                                        <p class="font-bold text-slate-900">Cash on Delivery</p>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer group opacity-50">
                                    <input type="radio" name="payment" value="card" disabled class="peer sr-only">
                                    <div class="p-6 border-2 border-slate-100 rounded-2xl">
                                        <i class="fas fa-credit-card text-2xl mb-2 text-slate-400"></i>
                                        <p class="font-bold text-slate-900">Credit Card (Soon)</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="pt-10 border-t border-slate-100 mt-10 space-y-2">
                            <div class="flex justify-between items-center text-slate-500">
                                <span>Subtotal</span>
                                <span class="font-bold">$<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <div class="flex justify-between items-center text-slate-500">
                                <span>Delivery Fee</span>
                                <?php if ($shipping == 0): ?>
                                    <span class="font-bold text-green-600">Free</span>
                                <?php else: ?>
                                    <span class="font-bold">$<?php echo number_format($shipping, 2); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex justify-between items-center pt-4 border-t border-slate-50 mt-4">
                                <span class="text-slate-900 font-bold text-lg">Total Amount</span>
                                <span class="text-3xl font-black text-slate-900">$<?php echo number_format($total, 2); ?></span>
                            </div>
                            <button type="submit" class="w-full bg-orange-600 text-white py-6 rounded-2xl font-black text-xl hover:bg-orange-700 transition shadow-2xl shadow-orange-200">
                                Place Order Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
