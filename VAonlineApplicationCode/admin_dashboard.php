<?php
include 'connection.php'; // Include your database connection file

// Fetch users from the database
$sql = "SELECT * FROM users"; // You may adjust the table name if necessary
$result = $conn->query($sql);

if ($result === false) {
    die("Error fetching users: " . $conn->error);
}

$users = [];
if ($result->num_rows > 0) {
    // Fetch all users into an array
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="container">
        <h1>User Management</h1>
        <table>
            <thead>
                <tr>
                    <th>UserID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>User Type</th>
                    <th>Profile Picture</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['UserID']); ?></td>
                    <td><?php echo htmlspecialchars($user['Username']); ?></td>
                    <td><?php echo htmlspecialchars($user['Email']); ?></td>
                    <td><?php echo htmlspecialchars($user['UserType']); ?></td>
                    <td>
                        <img src="<?php echo htmlspecialchars($user['ProfilePicture']); ?>" alt="Profile Picture" width="50">
                    </td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $user['UserID']; ?>">Edit</a>
                        <a href="delete_user.php?id=<?php echo $user['UserID']; ?>" 
                           onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6">No users found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="create_user.php">Create New User</a> <!-- Link to create a new user -->
    </div>
</body>
</html>