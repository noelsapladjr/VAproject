<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
include 'connection.php';

// Start the session
session_start();

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute the SQL query to check for the user
    $sql = "SELECT * FROM users WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("MySQL prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['Password'])) {
            // Password is correct, check UserType and redirect accordingly
            $_SESSION['UserID'] = $user['UserID']; // Store User ID in session
            
            // Using ENUM values correctly
            $userType = $user['UserType'];
            echo "UserType: " . $userType; // Debugging output for UserType
            
            if ($userType === 'VA') {
                header("Location: VADashboard.php");
                exit();
            } else if ($userType === 'Client') {
                header("Location: ClientDashboard.php");
                exit();
            } else {
                echo "Unexpected UserType: " . $userType; // For debugging purposes
                header("Location: login.php");
                exit();
            }
        } else {
            // Password is incorrect
            $error = "Invalid email or password.";
        }
    } else {
        // User does not exist
        $error = "Invalid email or password.";
    }
}

// Close the connection
$conn->close();
?>

<!-- Display error message if exists -->
<?php if (isset($error)): ?>
    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VA Online Job Application</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <img src="FinalB.jpg" alt="Description" class="responsive-image">
    <header>
        <h2 class="logo">VA Online Job Application</h2>
        <nav class="navigation">
            <a href="#">Home</a>
            <a href="#">About</a>
            <a href="#">Service</a>
            <a href="#">Contact</a>
            <button class="btnlogin-pop">LogIn</button>
        </nav>
    </header>
    <div class="wrapper">
        <span class="icon-close"><ion-icon name="close"></ion-icon></span>
        <div class="form-box login">
            <h2>LOGIN</h2>
            <form action="login.php" method="POST">
                <div class="input-box">
                    <span class="icon"><ion-icon name="email"></ion-icon></span>
                    <input type="text" name="email" required>
                    <label>Email</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                    <input type="password" name="password" required>
                    <label>Password</label>
                </div>
                <div class="remember-forgot">
                    <label><input type="checkbox">Remember me</label>
                    <a href="#">Forgot Password</a>
                </div>
                <button type="submit" class="btn">Log In</button>
                <div class="login-register"><p>Don't have an account?<a href="#" class="register-link">Register</a></p></div>
            </form>
        </div>

        <div class="form-box register">
            <h2>Registration</h2>
            <form action="register.php" method="POST">
                <div class="input-box">
                    <span class="icon"><ion-icon name="person"></ion-icon></span>
                    <input type="text" name="username" required>
                    <label>Username</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="mail"></ion-icon></span>
                    <input type="email" name="email" required>
                    <label>Email</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                    <input type="password" name="password" required>
                    <label>Password</label>
                </div>
                <div class="remember-forgot">
                    <label><input type="checkbox" required>I agree to the terms & conditions</label>
                </div>
                <button type="submit" class="btn">Register</button>
                <div class="login-register"><p>Already have an account?<a href="#" class="login-link">Login</a></p></div>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>