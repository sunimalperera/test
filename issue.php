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
}

if (isset($_POST['issue_part'])) {
    $id = $_POST['id'];
    $issue_quantity = (int)$_POST['issue_quantity'];

    // Update issued quantity
    $stmt = $conn->prepare("UPDATE spare_parts SET issued_quantity = issued_quantity + ? WHERE id = ?");
    $stmt->execute([$issue_quantity, $id]);

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Spare Part</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Issue Spare Part</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="form-container">
        <form method="POST">
            <input type="hidden" name="id" value="<?= $part['id'] ?>">
            <label for="issue_quantity">Issue Quantity:</label>
            <input type="number" name="issue_quantity" required>
            <button type="submit" name="issue_part">Issue Part</button>
        </form>
    </div>
</body>
</html>