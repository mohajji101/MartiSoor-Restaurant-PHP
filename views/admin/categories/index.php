<?php
// views/admin/categories/index.php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login');
    exit;
}

include __DIR__ . '/../../partials/header.php';

$pdo = get_db_connection();
// Fetch categories
// Soo qaado qaybaha (categories)
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();
?>

<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-12">
            <div>
                <div class="flex items-center gap-4 mb-2">
                    <a href="../admin" class="text-slate-400 hover:text-orange-600 transition"><i
                            class="fas fa-chevron-left"></i></a>
                    <h1 class="text-4xl font-bold text-slate-900">Manage Categories</h1>
                </div>
                <p class="text-slate-500">Organize your menu by categories</p>
            </div>

            <a href="./categories/create"
                class="mt-6 md:mt-0 bg-blue-600 text-white px-8 py-4 rounded-2xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>Add New Category</span>
            </a>
        </div>

        <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-8 py-6">ID</th>
                            <th class="px-8 py-6">Category Name</th>
                            <th class="px-8 py-6">Created At</th>
                            <th class="px-8 py-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($categories as $cat): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-6 text-slate-400 font-mono">#<?php echo $cat['id']; ?></td>
                                <td class="px-8 py-6 font-bold text-slate-900"><?php echo htmlspecialchars($cat['name']); ?>
                                </td>
                                <td class="px-8 py-6 text-slate-500 text-sm">
                                    <?php echo date('M d, Y', strtotime($cat['created_at'])); ?></td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="./categories/edit?id=<?php echo $cat['id']; ?>"
                                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="./categories/delete?id=<?php echo $cat['id']; ?>"
                                            onclick="return confirm('Deleting this category will affect products. Continue?')"
                                            class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../partials/footer.php'; ?>