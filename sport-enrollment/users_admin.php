<?php
    include 'session_cookie_check.php';

    // Handle deletion if a delete request is made
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
        $delete_user_id = intval($_POST['delete_user_id']);

        // SQL to delete the user
        $delete_query = "DELETE FROM users WHERE user_id = ?";
        $stmt = $link->prepare($delete_query);

        if ($stmt) {
            $stmt->bind_param("i", $delete_user_id);
            $stmt->execute();
            $stmt->close();
        }

        // Redirect to the same page to reflect changes
        header("Location: users_admin.php");
        exit();
    }

    // Fetch all users
    $query = "SELECT user_id, first_name, last_name, phone_number, email FROM users";
    $result = $link->query($query);

    $users = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
?>
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Users</title>
        <link rel="stylesheet" type="text/css" href="style.css?v=1.7" />
    </head>
    <body>
        <div class="header-container">
            <h1>Welcome to Vlad Gym</h1>
        </div>
        
        <div class="navbar">
        
            <!-- Navigation links -->
            <div class="center-links">
                <a href="users_admin.php" class="logo">Users</a>
                <a href="messages_admin.php">Messages</a>
                <a href="statistics_admin.php">Statistics</a>
            </div>

            <div class="right-links">
                    <a href="logout.php">Log out</a>
            </div>
            
        </div>

        <div class="users-container">
            <h2>Users List</h2>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['user_id']) ?></td>
                            <td><?= htmlspecialchars($user['first_name']) ?></td>
                            <td><?= htmlspecialchars($user['last_name']) ?></td>
                            <td><?= htmlspecialchars($user['phone_number']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <form method="POST" action="users_admin.php" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="delete_user_id" value="<?= htmlspecialchars($user['user_id']) ?>">
                                    <button type="submit" class="delete-button">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </body>
</html>