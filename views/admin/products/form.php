<?php
// views/admin/products/form.php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login');
    exit;
}

$pdo = get_db_connection();
$id = $_GET['id'] ?? null;
$product = null;
$errors = [];

// Fetch product if editing
// Soo qaado alaabta haddii la bedelayo
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if (!$product) {
        header('Location: ./');
        exit;
    }
}

// Handle form submission
// Xakamee soo gudbinta foomka
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $price = $_POST['price'] ?? 0;
    $category_id = $_POST['category_id'] ?? null;
    $image = trim($_POST['image'] ?? '');

    if (empty($title))
        $errors[] = "Title is required.";
    if ($price <= 0)
        $errors[] = "Valid price is required.";

    if (empty($errors)) {
        if ($id) {
            // Update product
            // Cusbooneysii alaabta
            $stmt = $pdo->prepare("UPDATE products SET title = ?, price = ?, category_id = ?, image = ? WHERE id = ?");
            $stmt->execute([$title, $price, $category_id, $image, $id]);
        } else {
            // Create new product
            // Samee alaab cusub
            $stmt = $pdo->prepare("INSERT INTO products (title, price, category_id, image) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $price, $category_id, $image]);
        }
        header('Location: /PHP/Projects/Restaurant-Ordering-System/public/admin/products');
        exit;
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

include __DIR__ . '/../../partials/header.php';
?>

<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <div class="flex items-center gap-4 mb-2">
                <a href="../products" class="text-slate-400 hover:text-orange-600 transition"><i
                        class="fas fa-arrow-left"></i></a>
                <h1 class="text-4xl font-bold text-slate-900"><?php echo $id ? 'Edit Product' : 'Add New Product'; ?>
                </h1>
            </div>
            <p class="text-slate-500">Fill in the details to <?php echo $id ? 'update' : 'create'; ?> a menu item</p>
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="col-span-2">
                        <label for="title" class="block text-sm font-bold text-slate-700 mb-2">Product Title</label>
                        <input type="text" id="title" name="title"
                            value="<?php echo htmlspecialchars($product['title'] ?? ''); ?>" required
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-bold text-slate-700 mb-2">Price ($)</label>
                        <input type="number" step="0.01" id="price" name="price"
                            value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>" required
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-bold text-slate-700 mb-2">Category</label>
                        <select id="category_id" name="category_id"
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition appearance-none">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo (isset($product['category_id']) && $product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label for="image" class="block text-sm font-bold text-slate-700 mb-2">Image URL</label>
                        <input type="url" id="image" name="image"
                            value="<?php echo htmlspecialchars($product['image'] ?? ''); ?>"
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition"
                            placeholder="https://example.com/image.jpg">
                    </div>
                </div>

                <div class="pt-8 border-t border-slate-100 flex gap-4">
                    <button type="submit"
                        class="flex-1 bg-orange-600 text-white px-8 py-4 rounded-2xl font-bold shadow-lg shadow-orange-200 hover:bg-orange-700 transition">
                        <?php echo $id ? 'Update Product' : 'Create Product'; ?>
                    </button>
                    <a href="../products"
                        class="px-8 py-4 rounded-2xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../partials/footer.php'; ?>