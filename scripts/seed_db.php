<?php
// scripts/seed_db.php
require_once __DIR__ . '/../config/database.php';

$pdo = get_db_connection();

try {
    // 1. Seed Categories
    // 1. Abuur Qaybaha (Categories)
    $categories = ['Pizza', 'Salads', 'Desserts', 'Drinks', 'Burgers'];
    foreach ($categories as $name) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?) ON CONFLICT (name) DO NOTHING");
        $stmt->execute([$name]);
    }

    // Get category IDs
    // Soo hel ID-yada qaybaha
    $cat_ids = $pdo->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_KEY_PAIR);
    $cat_ids = array_flip($cat_ids);

    // 2. Seed Products
    // 2. Abuur Alaabta (Products)
    $products = [
        ['title' => 'Margherita Pizza', 'price' => 12.99, 'category_id' => $cat_ids['Pizza'], 'image' => 'https://images.unsplash.com/photo-1574071318508-1cdbad80ad38?auto=format&fit=crop&w=400&q=80'],
        ['title' => 'Pepperoni Feast', 'price' => 15.99, 'category_id' => $cat_ids['Pizza'], 'image' => 'https://images.unsplash.com/photo-1628840042765-356cda07504e?auto=format&fit=crop&w=400&q=80'],
        ['title' => 'Greek Salad', 'price' => 9.50, 'category_id' => $cat_ids['Salads'], 'image' => 'https://images.unsplash.com/photo-1540420753444-4ae030bc3b72?auto=format&fit=crop&w=400&q=80'],
        ['title' => 'Classic Cheeseburger', 'price' => 11.00, 'category_id' => $cat_ids['Burgers'], 'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=400&q=80'],
        ['title' => 'Chocolate Lava Cake', 'price' => 7.99, 'category_id' => $cat_ids['Desserts'], 'image' => 'https://images.unsplash.com/photo-1624353365286-3f8d62daad51?auto=format&fit=crop&w=400&q=80'],
    ];

    foreach ($products as $p) {
        // Insert product if not conflict
        // Geli alaabta haddii aysan jirin
        $stmt = $pdo->prepare("INSERT INTO products (title, price, category_id, image) VALUES (?, ?, ?, ?) ON CONFLICT DO NOTHING"); // Note: title is not unique, so this 'on conflict' might not work without a constraint
        // Better: just check if exists or blind insert for seed
        $stmt->execute([$p['title'], $p['price'], $p['category_id'], $p['image']]);
    }

    // 3. Seed Admin User
    // 3. Abuur User Admin ah
    $admin_email = 'admin@restaurant.com';
    $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES ('Admin User', ?, ?, 'admin') ON CONFLICT (email) DO NOTHING");
    $stmt->execute([$admin_email, $admin_pass]);

    echo "Database seeded successfully.\n";
    echo "Admin Login: admin@restaurant.com / admin123\n";

} catch (PDOException $e) {
    echo "Error seeding database: " . $e->getMessage() . "\n";
}
