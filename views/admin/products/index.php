<?php
// views/admin/products/index.php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login');
    exit;
}

include __DIR__ . '/../../partials/header.php';

$pdo = get_db_connection();
// Fetch products with category names
// Soo qaado alaabta iyo magacyada qaybahooda
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
$products = $stmt->fetchAll();
?>

<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-12">
            <div>
                <div class="flex items-center gap-4 mb-2">
                    <a href="../admin" class="text-slate-400 hover:text-orange-600 transition"><i
                            class="fas fa-chevron-left"></i></a>
                    <h1 class="text-4xl font-bold text-slate-900">Manage Products</h1>
                </div>
                <p class="text-slate-500">Add, edit, or remove items from your menu</p>
            </div>

            <a href="./products/create"
                class="mt-6 md:mt-0 bg-orange-600 text-white px-8 py-4 rounded-2xl font-bold shadow-lg shadow-orange-200 hover:bg-orange-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>Add New Product</span>
            </a>
        </div>

        <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-8 py-6">Product</th>
                            <th class="px-8 py-6">Category</th>
                            <th class="px-8 py-6">Price</th>
                            <th class="px-8 py-6">Created At</th>
                            <th class="px-8 py-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($products as $product): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <img src="<?php echo htmlspecialchars($product['image'] ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80'); ?>"
                                            class="w-12 h-12 rounded-xl object-cover">
                                        <div class="font-bold text-slate-900">
                                            <?php echo htmlspecialchars($product['title']); ?></div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span
                                        class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-bold uppercase">
                                        <?php echo htmlspecialchars($product['category_name'] ?: 'Uncategorized'); ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6 font-bold text-slate-900">
                                    $<?php echo number_format($product['price'], 2); ?></td>
                                <td class="px-8 py-6 text-slate-500 text-sm">
                                    <?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="./products/edit?id=<?php echo $product['id']; ?>"
                                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="./products/delete?id=<?php echo $product['id']; ?>"
                                            onclick="return confirm('Are you sure you want to delete this product?')"
                                            class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center">
                                    <i class="fas fa-box-open text-5xl text-slate-200 mb-4 block"></i>
                                    <span class="text-slate-400 font-medium">No products found. Start by adding one!</span>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../partials/footer.php'; ?>