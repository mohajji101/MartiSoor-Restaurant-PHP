<?php
// views/admin/settings.php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /login');
    exit;
}

$pdo = get_db_connection();
$success_msg = "";

// Handle Settings Update
// Xakamee cusbooneysiinta habeynta (settings)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings_to_update = [
        'restaurant_name',
        'contact_email',
        'contact_phone',
        'currency_symbol',
        'delivery_fee',
        'free_delivery_threshold',
        'tax_rate'
    ];

    foreach ($settings_to_update as $key) {
        if (isset($_POST[$key])) {
            // Update individual setting
            // Cusbooneysii hal habeyn (setting)
            update_setting($pdo, $key, trim($_POST[$key]));
        }
    }
    $success_msg = "Settings updated successfully!";
}

// Fetch latest settings
// Soo qaado habeynta ugu dambeysay
$settings = get_settings($pdo);

include __DIR__ . '/../../views/partials/header.php';
?>

<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <div class="flex items-center gap-4 mb-2">
                <a href="/admin" class="text-slate-400 hover:text-orange-600 transition"><i
                        class="fas fa-arrow-left"></i></a>
                <h1 class="text-4xl font-bold text-slate-900">System Settings</h1>
            </div>
            <p class="text-slate-500">Configure your restaurant application preferences</p>
        </div>

        <?php if ($success_msg): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-8">
                <p class="text-sm text-green-700 font-bold"><?php echo htmlspecialchars($success_msg); ?></p>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="grid grid-cols-1 gap-8">

            <!-- General Settings -->
            <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 p-10">
                <h3 class="text-xl font-bold text-slate-900 mb-8 border-b border-slate-50 pb-6">
                    <i class="fas fa-store text-orange-600 mr-2"></i> General Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Restaurant Name</label>
                        <input type="text" name="restaurant_name"
                            value="<?php echo htmlspecialchars($settings['restaurant_name'] ?? 'MartiSoor Restaurant'); ?>"
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Contact Email</label>
                        <input type="email" name="contact_email"
                            value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>"
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Contact Phone</label>
                        <input type="text" name="contact_phone"
                            value="<?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?>"
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                    </div>
                </div>
            </div>

            <!-- Financial & Delivery Settings -->
            <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 p-10">
                <h3 class="text-xl font-bold text-slate-900 mb-8 border-b border-slate-50 pb-6">
                    <i class="fas fa-coins text-green-600 mr-2"></i> Finance & Delivery
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Currency Symbol</label>
                        <input type="text" name="currency_symbol"
                            value="<?php echo htmlspecialchars($settings['currency_symbol'] ?? '$'); ?>"
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tax Rate (%)</label>
                        <input type="number" step="0.01" name="tax_rate"
                            value="<?php echo htmlspecialchars($settings['tax_rate'] ?? '0'); ?>"
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Delivery Fee</label>
                        <input type="number" step="0.01" name="delivery_fee"
                            value="<?php echo htmlspecialchars($settings['delivery_fee'] ?? '0'); ?>"
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                        <p class="text-xs text-slate-400 mt-2">Standard delivery charge for all orders.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Free Delivery Threshold</label>
                        <input type="number" step="0.01" name="free_delivery_threshold"
                            value="<?php echo htmlspecialchars($settings['free_delivery_threshold'] ?? '0'); ?>"
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                        <p class="text-xs text-slate-400 mt-2">Orders above this amount get free delivery.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-orange-600 text-white px-10 py-4 rounded-2xl font-bold shadow-lg shadow-orange-200 hover:bg-orange-700 transition flex items-center gap-2">
                    <i class="fas fa-save"></i> Save All Changes
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../views/partials/footer.php'; ?>