<?php
    include 'session_cookie_check.php';

    // Handle Accept/Decline Actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $request_id = intval($_POST['request_id']);
        $action = $_POST['action']; // 'accept' or 'decline'

        if ($action === 'accept') {
            // Accept the request by updating the status
            $query = "UPDATE trainer_course_requests SET status = 'accepted' WHERE request_id = ?";
        } elseif ($action === 'decline') {
            // Decline the request by updating the status
            $query = "UPDATE trainer_course_requests SET status = 'declined' WHERE request_id = ?";
        }

        $stmt = $link->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            $stmt->close();
        }

        // Redirect to the same page to reflect changes
        header("Location: messages_admin.php");
        exit();
    }

    // Fetch all trainer requests
    $query = "
        SELECT 
            tr.request_id, 
            tp.name AS program_name, 
            CONCAT(u.first_name, ' ', u.last_name) AS trainer_name, 
            tr.request_date AS request_date, 
            tr.status
        FROM 
            trainer_course_requests tr
        JOIN 
            training_programs tp ON tr.course_id = tp.program_id
        JOIN 
            users u ON tr.trainer_id = u.user_id
        WHERE 
            tr.status = 'pending'
        ORDER BY 
            tr.request_date DESC
    ";

    $result = $link->query($query);

    $requests = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" type="text/css" href="style.css?v=1.7" />
    <link rel="stylesheet" type="text/css" href="admin.css" />
</head>
<body>
    <div class="header-container">
        <h1>Welcome to Vlad Gym</h1>
    </div>
    
    <div class="navbar">
        <div class="center-links">
            <a href="users_admin.php" class="logo">Users</a>
            <a href="messages_admin.php">Messages</a>
            <a href="statistics_admin.php">Statistics</a>
        </div>

        <div class="right-links">
            <a href="logout.php">Log out</a>
        </div>
    </div>

    <div class="messages-container">
        <h2>Trainer Requests</h2>
        <table class="requests-table">
            <thead>
                <tr>
                    <th>Nr.</th>
                    <th>Program Name</th>
                    <th>Trainer</th>
                    <th>Request Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($requests)) : ?>
                    <tr>
                        <td colspan="5">No pending requests</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($requests as $index => $request) : ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($request['program_name']) ?></td>
                            <td><?= htmlspecialchars($request['trainer_name']) ?></td>
                            <td><?= htmlspecialchars(date("Y-m-d H:i", strtotime($request['request_date']))) ?></td>
                            <td>
                                <form method="POST" action="messages_admin.php" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                                    <input type="hidden" name="action" value="accept">
                                    <button type="submit" class="accept-button">Accept</button>
                                </form>
                                <form method="POST" action="messages_admin.php" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                                    <input type="hidden" name="action" value="decline">
                                    <button type="submit" class="decline-button">Decline</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
