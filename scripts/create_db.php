<?php
// scripts/create_db.php

// Database configuration
// Xogta Database-ka
$host = 'localhost';
$port = '5432';
$user = 'postgres';
$pass = '123';

try {
    // Connect to default postgres database
    // Ku xirnow database-ka postgres ee caadiga ah
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=postgres", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if database exists
    // Hubi haddii database-ku jiro
    $stmt = $pdo->prepare("SELECT 1 FROM pg_database WHERE datname = 'restaurant_db'");
    $stmt->execute();

    if (!$stmt->fetch()) {
        // Create the database if it doesn't exist
        // Samee database-ka haddii uusan jirin
        $pdo->exec("CREATE DATABASE restaurant_db");
        echo "Database 'restaurant_db' created successfully.\n";
    } else {
        echo "Database 'restaurant_db' already exists.\n";
    }
} catch (PDOException $e) {
    die("Error creating database: " . $e->getMessage());
}
