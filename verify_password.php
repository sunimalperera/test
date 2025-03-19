<?php
session_start();
include 'db.php'; // Ensure this file correctly connects to the database

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $entered_password = $_POST['password'];
    $action = $_POST['action'];
    $id = $_POST['id'];

    // Fetch the correct password from the database (Adjust this query based on your database structure)
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $stored_password = $stmt->fetchColumn();

    // Debugging: Output values for checking
    error_log("Entered Password: " . $entered_password);
    error_log("Stored Password: " . $stored_password);

    // If password is hashed, verify with password_verify
    if (password_verify($entered_password, $stored_password)) {
        echo json_encode(["success" => true, "redirect" => "dashboard.php"]);
    } else {
        echo json_encode(["success" => false]);
    }
}
?>
