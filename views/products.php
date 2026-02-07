<?php
// views/products.php
include 'partials/header.php';

$pdo = get_db_connection();

// Fetch Categories
// Soo qaado qaybaha (categories)
$categories_stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $categories_stmt->fetchAll();

// Filter Products by Category
// Kala sooc alaabta adigoo isticmaalaya qaybaha
$category_id = $_GET['category_id'] ?? null;
if ($category_id) {
    // Show products in specific category
    // Muuji alaabta qaybtaas ku jirta
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ?");
    $stmt->execute([$category_id]);
} else {
    // Show all products
    // Muuji dhammaan alaabta
    $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id");
}
$products = $stmt->fetchAll();
?>

<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-12">
            <div>
                <h1 class="text-4xl font-bold text-slate-900 mb-2">Our Delicious Menu</h1>
                <p class="text-slate-500">Pick your favorite and enjoy the taste</p>
            </div>

            <div class="mt-6 md:mt-0 flex gap-4 overflow-x-auto pb-4 md:pb-0 w-full md:w-auto">
                <a href="./products"
                    class="px-6 py-2 rounded-full whitespace-nowrap font-medium <?php echo !$category_id ? 'bg-orange-600 text-white shadow-lg' : 'bg-white text-slate-600 border border-slate-200 hover:border-orange-500'; ?> transition">
                    All Items
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="./products?category_id=<?php echo $cat['id']; ?>"
                        class="px-6 py-2 rounded-full whitespace-nowrap font-medium <?php echo $category_id == $cat['id'] ? 'bg-orange-600 text-white shadow-lg' : 'bg-white text-slate-600 border border-slate-200 hover:border-orange-500'; ?> transition">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (empty($products)): ?>
            <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-300">
                <i class="fas fa-utensils text-5xl text-slate-300 mb-4 block"></i>
                <h3 class="text-xl font-bold text-slate-400">No products found in this category.</h3>
                <a href="./products" class="text-orange-600 font-bold mt-4 inline-block hover:underline">View all menu</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($products as $product): ?>
                    <div
                        class="bg-white rounded-[2.5rem] overflow-hidden shadow-xl shadow-slate-200/50 group hover:-translate-y-2 transition-transform duration-300 border border-slate-100">
                        <div class="h-56 relative overflow-hidden">
                            <img src="<?php echo htmlspecialchars($product['image'] ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80'); ?>"
                                alt="<?php echo htmlspecialchars($product['title']); ?>"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <div class="absolute top-4 right-4">
                                <span
                                    class="bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-orange-600 shadow-sm">
                                    <?php echo htmlspecialchars($product['category_name'] ?: 'General'); ?>
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-slate-800 mb-2 truncate">
                                <?php echo htmlspecialchars($product['title']); ?></h3>
                            <div class="flex justify-between items-center mt-4">
                                <span
                                    class="text-2xl font-black text-slate-900">$<?php echo number_format($product['price'], 2); ?></span>
                                <?php if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'): ?>
                                    <form action="/cart/add" method="POST">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="action" value="add">
                                        <button type="submit"
                                            class="bg-slate-900 text-white w-12 h-12 rounded-2xl flex items-center justify-center hover:bg-orange-600 transition shadow-lg">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'partials/footer.php'; ?>