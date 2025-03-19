<?php
session_start();
include 'db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['add_part'])) {
    // Basic part details
    $part_name = htmlspecialchars($_POST['part_name']);
    $part_number = htmlspecialchars($_POST['part_number']);
    $quantity = (int)$_POST['quantity'];
    $reorder_level = (int)$_POST['reorder_level'];
    $bin_number = htmlspecialchars($_POST['bin_number']);

    // Handle photo upload (optional)
    $photo = null;
    if (!empty($_FILES['photo']['name'])) {
        $photo = $_FILES['photo']['name'];
        $photo_tmp = $_FILES['photo']['tmp_name'];
        move_uploaded_file($photo_tmp, "uploads/$photo");
    }

    // Insert part into the database
    $stmt = $conn->prepare("INSERT INTO spare_parts (part_name, part_number, quantity, reorder_level, bin_number, photo) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$part_name, $part_number, $quantity, $reorder_level, $bin_number, $photo]);

    // Get the ID of the newly inserted part
    $part_id = $conn->lastInsertId();

    // Insert supplier prices into part_prices table
    if (isset($_POST['supplier_id']) && isset($_POST['price'])) {
        $supplier_ids = $_POST['supplier_id'];
        $prices = $_POST['price'];
        foreach ($supplier_ids as $index => $supplier_id) {
            $price = (float)$prices[$index];
            $stmt = $conn->prepare("INSERT INTO part_prices (part_id, supplier_id, price) VALUES (?, ?, ?)");
            $stmt->execute([$part_id, $supplier_id, $price]);
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
    <title>Add Part</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Add New Part</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="part_name" placeholder="Part Name" required>
            <input type="text" name="part_number" placeholder="Part Number" required>
            <input type="number" name="quantity" placeholder="Quantity" required>
            <input type="number" name="reorder_level" placeholder="Reorder Level" required>
            <input type="text" name="bin_number" placeholder="Bin Number" required>
            <input type="file" name="photo" accept="image/*"> <!-- Removed "required" attribute -->

            <!-- Supplier Prices Section -->
            <div id="supplier-prices">
                <div class="supplier-price">
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
                </div>
            </div>
            <button type="button" onclick="addSupplierPrice()">Add Another Supplier</button>

            <button type="submit" name="add_part">Add Part</button>
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