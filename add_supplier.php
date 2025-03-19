<?php
session_start();
include 'db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['add_supplier'])) {
    $supplier_name = htmlspecialchars($_POST['supplier_name']);
    $contact_info = htmlspecialchars($_POST['contact_info']);

    // Insert new supplier
    $stmt = $conn->prepare("INSERT INTO suppliers (supplier_name, contact_info) VALUES (?, ?)");
    $stmt->execute([$supplier_name, $contact_info]);

    header("Location: suppliers.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Add New Supplier</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="form-container">
        <form method="POST">
            <input type="text" name="supplier_name" placeholder="Supplier Name" required>
            <input type="text" name="contact_info" placeholder="Contact Info (Email/Phone)">
            <button type="submit" name="add_supplier">Add Supplier</button>
        </form>
    </div>
</body>
</html>