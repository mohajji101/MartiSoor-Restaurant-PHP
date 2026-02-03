<?php
// src/Helpers/settings.php

function get_settings($pdo) {
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings");
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    } catch (PDOException $e) {
        return [];
    }
}

function get_setting($pdo, $key, $default = '') {
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['setting_value'] : $default;
    } catch (PDOException $e) {
        return $default;
    }
}

function update_setting($pdo, $key, $value) {
    try {
        // Check if exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM system_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $exists = $stmt->fetchColumn() > 0;

        $timestamp = date('Y-m-d H:i:s');
        
        if ($exists) {
            $stmt = $pdo->prepare("UPDATE system_settings SET setting_value = ?, updated_at = ? WHERE setting_key = ?");
            return $stmt->execute([$value, $timestamp, $key]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value, updated_at) VALUES (?, ?, ?)");
            return $stmt->execute([$key, $value, $timestamp]);
        }
    } catch (PDOException $e) {
        file_put_contents(__DIR__ . '/../../db_errors.txt', $e->getMessage() . PHP_EOL, FILE_APPEND);
        return false;
    }
}
