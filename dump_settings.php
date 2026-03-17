<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=skillspehle", "root", "");
    $stmt = $pdo->query("SELECT * FROM settings WHERE `key` LIKE 'mail_%'");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['key'] . ": " . $row['value'] . "\n";
    }
} catch (Exception $e) {
    echo "Error with localhost: " . $e->getMessage() . "\n";
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=skillspehle", "root", "");
        $stmt = $pdo->query("SELECT * FROM settings WHERE `key` LIKE 'mail_%'");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo $row['key'] . ": " . $row['value'] . "\n";
        }
    } catch (Exception $e2) {
        echo "Error with 127.0.0.1: " . $e2->getMessage() . "\n";
    }
}
