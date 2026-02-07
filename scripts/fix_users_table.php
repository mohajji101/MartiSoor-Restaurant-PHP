<?php
// scripts/fix_users_table.php
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = get_db_connection();

    echo "Attempting to add columns to users table...\n";

    // Try adding reset_token
    // Isku day inaad ku darto reset_token column
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN reset_token VARCHAR(255)");
        echo "Added column: reset_token\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'already exists') !== false) {
            echo "Column reset_token already exists.\n";
        } else {
            echo "Error adding reset_token: " . $e->getMessage() . "\n";
        }
    }

    // Try adding reset_expires_at
    // Isku day inaad ku darto reset_expires_at column
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN reset_expires_at TIMESTAMP");
        echo "Added column: reset_expires_at\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'already exists') !== false) {
            echo "Column reset_expires_at already exists.\n";
        } else {
            echo "Error adding reset_expires_at: " . $e->getMessage() . "\n";
        }
    }

    echo "Database update process completed.\n";

} catch (PDOException $e) {
    echo "Critical Error: " . $e->getMessage() . "\n";
}
