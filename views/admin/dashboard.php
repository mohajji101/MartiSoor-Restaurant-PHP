<?php
// views/admin/dashboard.php

// Check if user is admin
// Hubi haddii isticmaaluhu yahay maamule (admin)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login');
    exit;
}

include __DIR__ . '/../partials/header.php';

$pdo = get_db_connection();

// Dashboard Statistics
// Xogta Guud ee Dashboard-ka
$user_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$product_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$order_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total) FROM orders WHERE status = 'Completed'")->fetchColumn() ?: 0;

?>

<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-12">
            <div>
                <h1 class="text-4xl font-bold text-slate-900 mb-2">Admin Dashboard</h1>
                <p class="text-slate-500">Manage your restaurant operations</p>
            </div>

            <div class="flex gap-4 mt-6 md:mt-0">
                <a href="/admin/products"
                    class="bg-white border border-slate-200 px-6 py-3 rounded-xl font-bold flex items-center gap-2 hover:border-orange-500 transition shadow-sm">
                    <i class="fas fa-hamburger text-orange-500"></i>
                    Manage Products
                </a>
                <a href="/admin/categories"
                    class="bg-white border border-slate-200 px-6 py-3 rounded-xl font-bold flex items-center gap-2 hover:border-orange-500 transition shadow-sm">
                    <i class="fas fa-tags text-orange-500"></i>
                    Manage Categories
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <div
                class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 transition-transform hover:-translate-y-1">
                <div class="flex justify-between items-start mb-4">
                    <div class="bg-blue-50 w-12 h-12 rounded-2xl flex items-center justify-center text-blue-600">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-slate-900 mb-1"><?php echo $user_count; ?></div>
                <div class="text-slate-500 font-medium">Total Users</div>
            </div>

            <div
                class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 transition-transform hover:-translate-y-1">
                <div class="flex justify-between items-start mb-4">
                    <div class="bg-orange-50 w-12 h-12 rounded-2xl flex items-center justify-center text-orange-600">
                        <i class="fas fa-box text-xl"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-slate-900 mb-1"><?php echo $product_count; ?></div>
                <div class="text-slate-500 font-medium">Menu Items</div>
            </div>

            <div
                class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 transition-transform hover:-translate-y-1">
                <div class="flex justify-between items-start mb-4">
                    <div class="bg-green-50 w-12 h-12 rounded-2xl flex items-center justify-center text-green-600">
                        <i class="fas fa-shopping-cart text-xl"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-slate-900 mb-1"><?php echo $order_count; ?></div>
                <div class="text-slate-500 font-medium">Total Orders</div>
            </div>

            <div
                class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 transition-transform hover:-translate-y-1">
                <div class="flex justify-between items-start mb-4">
                    <div class="bg-purple-50 w-12 h-12 rounded-2xl flex items-center justify-center text-purple-600">
                        <i class="fas fa-dollar-sign text-xl"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-slate-900 mb-1">$<?php echo number_format($total_revenue, 2); ?>
                </div>
                <div class="text-slate-500 font-medium">Revenue</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Orders -->
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-slate-900">Recent Orders</h3>
                    <a href="/admin/orders" class="text-orange-600 font-bold text-sm">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-8 py-4">Order ID</th>
                                <th class="px-8 py-4">Customer</th>
                                <th class="px-8 py-4">Total</th>
                                <th class="px-8 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php
                            $recent_orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetchAll();
                            foreach ($recent_orders as $order):
                                ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-8 py-4 font-bold">#<?php echo $order['id']; ?></td>
                                    <td class="px-8 py-4">
                                        <div class="font-medium text-slate-900">
                                            <?php echo htmlspecialchars($order['user_name']); ?></div>
                                        <div class="text-xs text-slate-500">
                                            <?php echo htmlspecialchars($order['user_email']); ?></div>
                                    </td>
                                    <td class="px-8 py-4 font-bold text-slate-900">
                                        $<?php echo number_format($order['total'], 2); ?></td>
                                    <td class="px-8 py-4">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase <?php
                                        echo $order['status'] === 'Completed' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700';
                                        ?>">
                                            <?php echo $order['status']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Management Shortcuts -->
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-8">
                <h3 class="text-xl font-bold text-slate-900 mb-8">Quick Actions</h3>
                <div class="grid grid-cols-2 gap-4">
                    <a href="/admin/products/create"
                        class="flex flex-col items-center justify-center p-8 bg-slate-50 rounded-2xl hover:bg-orange-50 hover:text-orange-600 transition group border border-transparent hover:border-orange-200">
                        <div
                            class="w-12 h-12 rounded-full bg-white flex items-center justify-center shadow-md mb-4 group-hover:bg-orange-600 group-hover:text-white transition">
                            <i class="fas fa-plus"></i>
                        </div>
                        <span class="font-bold">Add Product</span>
                    </a>

                    <a href="/admin/categories"
                        class="flex flex-col items-center justify-center p-8 bg-slate-50 rounded-2xl hover:bg-blue-50 hover:text-blue-600 transition group border border-transparent hover:border-blue-200">
                        <div
                            class="w-12 h-12 rounded-full bg-white flex items-center justify-center shadow-md mb-4 group-hover:bg-blue-600 group-hover:text-white transition">
                            <i class="fas fa-list"></i>
                        </div>
                        <span class="font-bold">Categories</span>
                    </a>

                    <a href="/admin/users"
                        class="flex flex-col items-center justify-center p-8 bg-slate-50 rounded-2xl hover:bg-green-50 hover:text-green-600 transition group border border-transparent hover:border-green-200">
                        <div
                            class="w-12 h-12 rounded-full bg-white flex items-center justify-center shadow-md mb-4 group-hover:bg-green-600 group-hover:text-white transition">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <span class="font-bold">User Roles</span>
                    </a>

                    <a href="/admin/settings"
                        class="flex flex-col items-center justify-center p-8 bg-slate-50 rounded-2xl hover:bg-purple-50 hover:text-purple-600 transition group border border-transparent hover:border-purple-200">
                        <div
                            class="w-12 h-12 rounded-full bg-white flex items-center justify-center shadow-md mb-4 group-hover:bg-purple-600 group-hover:text-white transition">
                            <i class="fas fa-cog"></i>
                        </div>
                        <span class="font-bold">Settings</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>