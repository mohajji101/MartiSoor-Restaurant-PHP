<?php
// config/database.php

// Database Credentials
// Xogta Gelitaanka Database-ka
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'restaurant_db'); // Database name / Magaca Database-ka
define('DB_USER', 'postgres');      // Username / Magaca Isticmaalaha
define('DB_PASS', '123');      // Password / Furaha

/**
 * Get Database Connection
 * Hel Khadka Xiriirka Database-ka (PDO)
 */
function get_db_connection()
{
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
    try {
        // Create new PDO instance
        // Abuur PDO instance cusub
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        // Stop execution if connection fails
        // Jooji shaqada haddii xiriirku guuldareysto
        die("Connection failed: " . $e->getMessage());
    }
}
