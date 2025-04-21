<?php
// Include the database connection file
include 'connection.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $email = $_POST['email'];
    $pass = $_POST['password']; // Store the password as plain text

    // SQL query to insert data into the users table
    $sql = "INSERT INTO users (Username, Password, Email) VALUES ('$user', '$pass', '$email')";

    if ($conn->query($sql) === TRUE) {
        // Registration successful, redirect to login page
        header("Location: VADashboard.php"); // Change 'login.php' to the actual path of your login page
        exit(); // Make sure to call exit after header to stop further execution
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the connection
$conn->close();
?>