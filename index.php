<?php
session_start();
include 'db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all spare parts
$stmt = $conn->query("SELECT *, (quantity - issued_quantity) AS current_stock FROM spare_parts");
$parts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spare Parts Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Bossong Spare Parts Management System</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="form-container">
        <h2>Spare Parts List</h2>
        <div class="export-options">
            <a href="export.php?type=excel">Export to Excel</a>
            <a href="export.php?type=pdf">Export to PDF</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Part Name</th>
                    <th>Part Number</th>
                    <th>Quantity</th>
                    <th>Issued</th>
                    <th>Stock</th>
                    <th>Reorder Level</th>
                    <th>Bin Number</th>
                    <th>Supplier Prices</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($parts as $part): ?>
                    <tr class="<?= ($part['current_stock'] <= $part['reorder_level']) ? 'reorder' : '' ?>">
                        <td><?= $part['id'] ?></td>
                        <td><?= $part['part_name'] ?></td>
                        <td><?= $part['part_number'] ?></td>
                        <td><?= $part['quantity'] ?></td>
                        <td><?= $part['issued_quantity'] ?></td>
                        <td><?= $part['current_stock'] ?></td>
                        <td><?= $part['reorder_level'] ?></td>
                        <td><?= $part['bin_number'] ?></td>
                        <td>
                            <?php
                            $stmt = $conn->prepare("SELECT suppliers.supplier_name, part_prices.price FROM part_prices 
                                                    JOIN suppliers ON part_prices.supplier_id = suppliers.id 
                                                    WHERE part_prices.part_id = ?");
                            $stmt->execute([$part['id']]);
                            $supplier_prices = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (!empty($supplier_prices)) {
                                foreach ($supplier_prices as $supplier_price) {
                                    echo "{$supplier_price['supplier_name']}: $" . number_format($supplier_price['price'], 2) . "<br>";
                                }
                            } else {
                                echo "No supplier prices available.";
                            }
                            ?>
                        </td>
                        <td>
                            <a href="issue.php?id=<?= $part['id'] ?>">Issue</a> |
                            <a href="edit.php?id=<?= $part['id'] ?>">Edit</a> |
                            <a href="delete.php?id=<?= $part['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>