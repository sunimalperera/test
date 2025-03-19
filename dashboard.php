<?php 
session_start(); // Ensure this is at the very top
include 'db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch spare parts data
$search = isset($_GET['search']) ? $_GET['search'] : '';
if ($search) {
    $stmt = $conn->prepare("SELECT DISTINCT *, (quantity - issued_quantity) AS current_stock FROM spare_parts WHERE part_name LIKE ? OR part_number LIKE ?");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $conn->query("SELECT DISTINCT *, (quantity - issued_quantity) AS current_stock FROM spare_parts");
}
$parts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch supplier prices for all parts at once
$supplierPrices = [];
$stmt = $conn->query("SELECT part_prices.part_id, suppliers.supplier_name, part_prices.price 
                      FROM part_prices 
                      JOIN suppliers ON part_prices.supplier_id = suppliers.id");
$supplierData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organize supplier prices by part ID
foreach ($supplierData as $row) {
    $supplierPrices[$row['part_id']][] = [
        'supplier_name' => $row['supplier_name'],
        'price' => $row['price']
    ];
}

// Assign supplier prices to each part and remove duplicates
$uniqueParts = [];
foreach ($parts as $part) {
    if (!isset($uniqueParts[$part['id']])) {
        $part['supplier_prices'] = $supplierPrices[$part['id']] ?? [];
        $uniqueParts[$part['id']] = $part;
    }
}
$parts = array_values($uniqueParts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    
</head>
<body>

<!-- Password Verification Modal -->
<div id="passwordModal" class="modal" style="display:none;">
    <div class="modal-content">
        <h3>Enter Password to Proceed</h3>
        <form id="passwordForm">
            <input type="password" id="passwordInput" placeholder="Enter your password" required style="width: 90%; padding: 12px; font-size: 16px; border: 1px solid #ccc; border-radius: 5px; margin-bottom: 10px;">
            <input type="hidden" id="actionType">
            <input type="hidden" id="recordId">
            <button type="submit">Verify</button>
            <button type="button" onclick="closeModal()">Cancel</button>
        </form>
        <p id="errorMessage" style="color:red; display:none;">Incorrect Password!</p>
    </div>
</div>

<script>
function requestPassword(action, id, url) {
    document.getElementById('actionType').value = action;
    document.getElementById('recordId').value = id;
    document.getElementById('passwordModal').style.display = 'block';

    document.getElementById('passwordForm').onsubmit = function(event) {
        event.preventDefault();
        let password = document.getElementById('passwordInput').value;

        fetch('verify_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `password=${password}&action=${action}&id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = url;
            } else {
                document.getElementById('errorMessage').style.display = 'block';
            }
        });
    };
}

function closeModal() {
    document.getElementById('passwordModal').style.display = 'none';
    document.getElementById('errorMessage').style.display = 'none';
}
</script>

<header>
    <h1>Welcome, <?= $_SESSION['username'] ?>!</h1>
    <small>Powered by Sunimal Perera</small>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="#" onclick="requestPassword('add_part', '', 'add_part.php')">Add Part</a>
        <a href="reports.php">Reports</a>
        <a href="#" onclick="requestPassword('add_supplier', '', 'add_supplier.php')">Add Supplier</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<h1>Bossong Hosiery</h1>

<div class="dashboard">
    <h2>Knitting Spare Parts List</h2>

    <!-- Search Form -->
    <form method="GET" action="dashboard.php" class="search-form" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search by part name or number" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

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
            <?php if (!empty($parts)): ?>
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
                            <?php if (!empty($part['supplier_prices'])): ?>
                                <button class="compare-prices" onclick="showSupplierPrices(<?= $part['id'] ?>)">Compare Prices</button>
                                <div id="supplier-prices-<?= $part['id'] ?>" class="supplier-prices-modal" style="display: none;">
                                    <h3>Supplier Prices for <?= $part['part_name'] ?></h3>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Supplier</th>
                                                <th>Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($part['supplier_prices'] as $supplier_price): ?>
                                                <tr>
                                                    <td><?= $supplier_price['supplier_name'] ?></td>
                                                    <td>$<?= number_format($supplier_price['price'], 2) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <button onclick="hideSupplierPrices(<?= $part['id'] ?>)">Close</button>
                                </div>
                            <?php else: ?>
                                No supplier prices available.
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="#" onclick="requestPassword('issue', <?= $part['id'] ?>, 'issue.php?id=<?= $part['id'] ?>')">Issue</a> |
                            <a href="#" onclick="requestPassword('edit', <?= $part['id'] ?>, 'edit.php?id=<?= $part['id'] ?>')">Edit</a> |
                            <a href="#" onclick="requestPassword('delete', <?= $part['id'] ?>, 'delete.php?id=<?= $part['id'] ?>')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10">No parts found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function showSupplierPrices(partId) {
    document.getElementById(`supplier-prices-${partId}`).style.display = 'block';
}
function hideSupplierPrices(partId) {
    document.getElementById(`supplier-prices-${partId}`).style.display = 'none';
}
</script>

</body>
</html>
