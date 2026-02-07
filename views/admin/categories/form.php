<?php
// views/admin/categories/form.php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login');
    exit;
}

$pdo = get_db_connection();
$id = $_GET['id'] ?? null;
$category = null;
$errors = [];

// Fetch category if editing
// Soo qaado qaybta haddii la bedelayo
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch();
    if (!$category) {
        header('Location: ./');
        exit;
    }
}

// Handle form submission
// Xakamee soo gudbinta foomka
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if (empty($name))
        $errors[] = "Category name is required.";

    if (empty($errors)) {
        try {
            if ($id) {
                // Update category
                // Cusbooneysii qaybta
                $stmt = $pdo->prepare("UPDATE categories SET name = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([$name, $id]);
            } else {
                // Create new category
                // Samee qayb cusub
                $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
                $stmt->execute([$name]);
            }
            header('Location: /PHP/Projects/Restaurant-Ordering-System/public/admin/categories');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Category name must be unique.";
        }
    }
}

include __DIR__ . '/../../partials/header.php';
?>

<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <div class="flex items-center gap-4 mb-2">
                <a href="../categories" class="text-slate-400 hover:text-orange-600 transition"><i
                        class="fas fa-arrow-left"></i></a>
                <h1 class="text-4xl font-bold text-slate-900"><?php echo $id ? 'Edit Category' : 'Add New Category'; ?>
                </h1>
            </div>
            <p class="text-slate-500">Define a new category for your menu items</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8">
                <p class="text-sm text-red-700">
                    <?php foreach ($errors as $error)
                        echo htmlspecialchars($error) . '<br>'; ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 p-10">
            <form action="" method="POST" class="space-y-8">
                <div>
                    <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Category Name</label>
                    <input type="text" id="name" name="name"
                        value="<?php echo htmlspecialchars($category['name'] ?? ''); ?>" required
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition"
                        placeholder="e.g. Seafood, Grill, Beverages">
                </div>

                <div class="pt-8 border-t border-slate-100 flex gap-4">
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white px-8 py-4 rounded-2xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition">
                        <?php echo $id ? 'Update Category' : 'Create Category'; ?>
                    </button>
                    <a href="../categories"
                        class="px-8 py-4 rounded-2xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../partials/footer.php'; ?>