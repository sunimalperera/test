<?php
$host = 'localhost';
$dbname = 'inventory_db';
$username = 'root'; // Default XAMPP username
$password = ''; // Default XAMPP password

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>