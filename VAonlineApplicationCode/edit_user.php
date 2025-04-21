<?php
include 'connection.php'; // Include your database connection file

// Check if the 'UserID' parameter is set in the URL
if (!isset($_GET['id'])) {
    echo "UserID not provided.";
    exit();
}

$userId = $_GET['id']; // Use 'UserID' to match the URL parameter

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate necessary POST data
    if (!isset($_POST['UserID'], $_POST['Username'], $_POST['Email'], $_POST['UserType'])) {
        echo "Required fields are missing.";
        exit();
    }

    $userId = $_POST['UserID'];
    $username = $_POST['Username'];
    $email = $_POST['Email'];
    $userType = $_POST['UserType'];

    $profilePicture = $_FILES['ProfilePicture']['name'];
    $uploadDir = 'uploads/'; // Define the upload directory

    // Check if the upload directory exists, if not, create it
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Create the directory if it doesn't exist
    }

    $uploadFile = $uploadDir . basename($profilePicture);
    $fileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

    // Check if a file was uploaded and validate image type
    if (!empty($profilePicture)) {
        // Validate file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowedTypes)) {
            // Move the uploaded file to the directory
            if (move_uploaded_file($_FILES['ProfilePicture']['tmp_name'], $uploadFile)) {
                // Prepare the statement if the file upload was successful
                $stmt = $conn->prepare("UPDATE users SET Username = ?, Email = ?, UserType = ?, ProfilePicture = ? WHERE UserID = ?");
                $stmt->bind_param("ssssi", $username, $email, $userType, $uploadFile, $userId);
            } else {
                echo "File upload error: unable to move uploaded file.";
                exit();
            }
        } else {
            echo "Unsupported file type.";
            exit();
        }
    } else {
        // No new file uploaded, just update other fields
        $stmt = $conn->prepare("UPDATE users SET Username = ?, Email = ?, UserType = ? WHERE UserID = ?");
        $stmt->bind_param("sssi", $username, $email, $userType, $userId);
    }

    // Execute the update
    if ($stmt->execute()) {
        // Redirect to the admin dashboard
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Update failed: " . $stmt->error;
        exit();
    }
}

// Fetch user details for editing
$stmt = $conn->prepare("SELECT * FROM users WHERE UserID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="container">
        <h1>Edit User</h1>
        <form action="edit_user.php?id=<?php echo htmlspecialchars($user['UserID']); ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="UserID" value="<?php echo htmlspecialchars($user['UserID']); ?>">
            <div>
                <label for="Username">Username:</label>
                <input type="text" name="Username" id="Username" value="<?php echo htmlspecialchars($user['Username']); ?>" required>
            </div>
            <div>
                <label for="Email">Email:</label>
                <input type="email" name="Email" id="Email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
            </div>
            <div>
                <label for="UserType">User Type:</label>
                <select name="UserType" id="UserType" required>
                    <option value="Client" <?php echo ($user['UserType'] == 'Client') ? 'selected' : ''; ?>>Client</option>
                    <option value="VA" <?php echo ($user['UserType'] == 'VA') ? 'selected' : ''; ?>>VA</option>
                </select>
            </div>
            <div>
                <label for="ProfilePicture">Profile Picture:</label>
                <input type="file" name="ProfilePicture" id="ProfilePicture" accept="image/*">
                <img src="<?php echo htmlspecialchars($user['ProfilePicture']); ?>" alt="Current Profile Picture" width="50">
            </div>
            <div>
                <button type="submit">Update User</button>
            </div>
        </form>
        <a href="admin_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>