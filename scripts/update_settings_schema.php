<?php
// scripts/update_settings_schema.php
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = get_db_connection();

    // Create settings table
    // Samee jadwalka habeynta (settings table)
    $sql = "
    CREATE TABLE IF NOT EXISTS system_settings (
        setting_key VARCHAR(50) PRIMARY KEY,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";
    $pdo->exec($sql);
    echo "Table 'system_settings' checked/created.\n";

    // Seed default values
    // Geli qiimayaasha aasaasiga ah (default values)
    $defaults = [
        'restaurant_name' => 'MartiSoor Restaurant',
        'currency_symbol' => '$',
        'delivery_fee' => '3.00',
        'free_delivery_threshold' => '50.00',
        'tax_rate' => '0',
        'contact_email' => 'info@martisoor.com',
        'contact_phone' => '+252 61 5000000'
    ];

    $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON CONFLICT (setting_key) DO NOTHING");

    foreach ($defaults as $key => $value) {
        $stmt->execute([$key, $value]);
    }
    echo "Default settings seeded.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
