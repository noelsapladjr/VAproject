<?php
include 'connection.php'; // Include your database connection file

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM users WHERE UserID = ?");
    $stmt->bind_param("i", $userId); // "i" means integer
    $stmt->execute();

    // Redirect to the admin dashboard
    header("Location: admin_dashboard.php");
    exit();
} else {
    echo "No user ID provided.";
}
?>