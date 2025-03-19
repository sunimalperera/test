<?php
session_start();
include 'db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all suppliers
$stmt = $conn->query("SELECT * FROM suppliers");
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppliers</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Suppliers</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="add_supplier.php">Add Supplier</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="dashboard">
        <table>
            <thead>
                <tr>
                    <th>Supplier Name</th>
                    <th>Contact Info</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($suppliers as $supplier): ?>
                    <tr>
                        <td><?= $supplier['supplier_name'] ?></td>
                        <td><?= $supplier['contact_info'] ?></td>
                        <td>
                            <a href="edit_supplier.php?id=<?= $supplier['id'] ?>">Edit</a> |
                            <a href="delete_supplier.php?id=<?= $supplier['id'] ?>">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>