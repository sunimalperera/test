<?php
session_start();
include 'db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM spare_parts WHERE id = ?");
    $stmt->execute([$id]);
    $part = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch supplier prices
    $stmt = $conn->prepare("SELECT * FROM part_prices WHERE part_id = ?");
    $stmt->execute([$id]);
    $supplier_prices = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_POST['update_part'])) {
    $id = $_POST['id'];
    $part_name = htmlspecialchars($_POST['part_name']);
    $part_number = htmlspecialchars($_POST['part_number']);
    $quantity = (int)$_POST['quantity'];
    $reorder_level = (int)$_POST['reorder_level'];
    $bin_number = htmlspecialchars($_POST['bin_number']);

    // Update part details
    $stmt = $conn->prepare("UPDATE spare_parts SET part_name = ?, part_number = ?, quantity = ?, reorder_level = ?, bin_number = ? WHERE id = ?");
    $stmt->execute([$part_name, $part_number, $quantity, $reorder_level, $bin_number, $id]);

    // Update supplier prices
    if (isset($_POST['supplier_id']) && isset($_POST['price'])) {
        $supplier_ids = $_POST['supplier_id'];
        $prices = $_POST['price'];

        // Delete existing prices
        $stmt = $conn->prepare("DELETE FROM part_prices WHERE part_id = ?");
        $stmt->execute([$id]);

        // Insert new prices
        foreach ($supplier_ids as $index => $supplier_id) {
            $price = (float)$prices[$index];
            $stmt = $conn->prepare("INSERT INTO part_prices (part_id, supplier_id, price) VALUES (?, ?, ?)");
            $stmt->execute([$id, $supplier_id, $price]);
        }
    }

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Spare Part</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Edit Spare Part</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="form-container">
        <form method="POST">
            <input type="hidden" name="id" value="<?= $part['id'] ?>">
            <input type="text" name="part_name" value="<?= $part['part_name'] ?>" required>
            <input type="text" name="part_number" value="<?= $part['part_number'] ?>" required>
            <input type="number" name="quantity" value="<?= $part['quantity'] ?>" required>
            <input type="number" name="reorder_level" value="<?= $part['reorder_level'] ?>" required>
            <input type="text" name="bin_number" value="<?= $part['bin_number'] ?>" required>

            <!-- Supplier Prices Section -->
            <div id="supplier-prices">
                <?php foreach ($supplier_prices as $supplier_price): ?>
                    <div class="supplier-price">
                        <select name="supplier_id[]" required>
                            <option value="">Select Supplier</option>
                            <?php
                            $stmt = $conn->query("SELECT * FROM suppliers");
                            while ($supplier = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $selected = $supplier['id'] == $supplier_price['supplier_id'] ? 'selected' : '';
                                echo "<option value='{$supplier['id']}' $selected>{$supplier['supplier_name']}</option>";
                            }
                            ?>
                        </select>
                        <input type="number" step="0.01" name="price[]" value="<?= $supplier_price['price'] ?>" required>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" onclick="addSupplierPrice()">Add Another Supplier</button>

            <button type="submit" name="update_part">Update Part</button>
        </form>
    </div>

    <script>
        // JavaScript function to add more supplier price fields
        function addSupplierPrice() {
            const container = document.getElementById('supplier-prices');
            const newField = document.createElement('div');
            newField.className = 'supplier-price';
            newField.innerHTML = `
                <select name="supplier_id[]" required>
                    <option value="">Select Supplier</option>
                    <?php
                    $stmt = $conn->query("SELECT * FROM suppliers");
                    while ($supplier = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$supplier['id']}'>{$supplier['supplier_name']}</option>";
                    }
                    ?>
                </select>
                <input type="number" step="0.01" name="price[]" placeholder="Price" required>
            `;
            container.appendChild(newField);
        }
    </script>
</body>
</html>