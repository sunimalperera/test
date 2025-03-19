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

    try {
        // Start a transaction
        $conn->beginTransaction();

        // Step 1: Delete related records in the part_prices table
        $stmt = $conn->prepare("DELETE FROM part_prices WHERE part_id = ?");
        $stmt->execute([$id]);

        // Step 2: Delete the part from the spare_parts table
        $stmt = $conn->prepare("DELETE FROM spare_parts WHERE id = ?");
        $stmt->execute([$id]);

        // Commit the transaction
        $conn->commit();

        // Redirect back to the dashboard
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        // Rollback the transaction in case of an error
        $conn->rollBack();
        die("Error deleting part: " . $e->getMessage());
    }
} else {
    // If no ID is provided, redirect to the dashboard
    header("Location: dashboard.php");
    exit();
}
?>