<?php
// views/cart.php
include 'partials/header.php';

$cart = $_SESSION['cart'] ?? [];
$subtotal = 0;

// Calculate Subtotal
// Xisaabi wadarta guud ee alaabta
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$pdo = get_db_connection();
// Get delivery settings
// Soo qaado habeynta gaarsiinta
$delivery_fee_setting = get_setting($pdo, 'delivery_fee', '0.00');
$free_delivery_threshold_setting = get_setting($pdo, 'free_delivery_threshold', '0.00');

$delivery_fee = (float)$delivery_fee_setting;
$free_delivery_threshold = (float)$free_delivery_threshold_setting;

// Calculate Shipping
// Xisaabi lacagta gaarsiinta
if ($subtotal > 0) {
    // Check for free delivery threshold
    // Hubi heerka gaarsiinta bilaashka ah
    if ($free_delivery_threshold > 0 && $subtotal >= $free_delivery_threshold) {
        $shipping = 0;
    } else {
        $shipping = $delivery_fee;
    }
} else {
    $shipping = 0;
}

$total = $subtotal + $shipping;
?>

<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-slate-900 mb-12">Your Shopping Bag</h1>
        
        <?php if (empty($cart)): ?>
            <div class="bg-white rounded-[3rem] p-20 text-center shadow-xl border border-slate-100">
                <div class="w-32 h-32 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8">
                    <i class="fas fa-shopping-bag text-5xl text-slate-200"></i>
                </div>
                <h2 class="text-3xl font-bold text-slate-900 mb-4">Your bag is empty</h2>
                <p class="text-slate-500 mb-10 max-w-sm mx-auto">Looks like you haven't added anything yet. Discover our delicious menu items!</p>
                <a href="./products" class="inline-block bg-orange-600 text-white px-10 py-4 rounded-full font-bold shadow-xl shadow-orange-200 hover:bg-orange-700 transition">
                    Browse Menu
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-6">
                    <?php foreach ($cart as $id => $item): ?>
                        <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 flex flex-col md:flex-row items-center gap-6">
                            <img src="<?php echo htmlspecialchars($item['image'] ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80'); ?>" class="w-32 h-32 rounded-3xl object-cover">
                            
                            <div class="flex-1 text-center md:text-left">
                                <h3 class="text-xl font-bold text-slate-900 mb-1"><?php echo htmlspecialchars($item['title']); ?></h3>
                                <p class="text-orange-600 font-bold">$<?php echo number_format($item['price'], 2); ?></p>
                            </div>
                            
                            <div class="flex items-center gap-4 bg-slate-50 p-2 rounded-2xl">
                                <form action="./cart/add" method="POST" class="inline">
                                    <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="quantity" value="<?php echo $item['quantity'] - 1; ?>">
                                    <button class="w-10 h-10 rounded-xl bg-white flex items-center justify-center hover:bg-slate-200 transition">-</button>
                                </form>
                                <span class="font-bold text-lg w-8 text-center"><?php echo $item['quantity']; ?></span>
                                <form action="./cart/add" method="POST" class="inline">
                                    <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="quantity" value="<?php echo $item['quantity'] + 1; ?>">
                                    <button class="w-10 h-10 rounded-xl bg-white flex items-center justify-center hover:bg-slate-200 transition">+</button>
                                </form>
                            </div>
                            
                            <div class="text-right">
                                <p class="text-xl font-black text-slate-900">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                                <form action="./cart/add" method="POST" class="mt-2">
                                    <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                    <input type="hidden" name="action" value="remove">
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-bold">Remove</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl border border-slate-100 sticky top-24">
                        <h3 class="text-2xl font-bold text-slate-900 mb-8">Order Summary</h3>
                        
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between text-slate-500">
                                <span>Subtotal</span>
                                <span class="font-bold text-slate-900">$<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <div class="flex justify-between text-slate-500">
                                <span>Delivery Fee</span>
                                <span class="font-bold text-slate-900">$<?php echo number_format($shipping, 2); ?></span>
                            </div>
                            <div class="pt-4 border-t border-slate-100 flex justify-between">
                                <span class="text-lg font-bold text-slate-900">Total</span>
                                <span class="text-3xl font-black text-orange-600">$<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>
                        
                        <a href="./checkout" class="block w-full bg-slate-900 text-white text-center py-5 rounded-2xl font-bold text-lg hover:bg-orange-600 transition shadow-xl shadow-slate-200">
                            Proceed to Checkout
                        </a>
                        
                        <div class="mt-6 flex items-center justify-center gap-2 text-slate-400 text-sm">
                            <i class="fas fa-lock text-xs"></i>
                            <span>Secure Checkout by MartiSoor</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
