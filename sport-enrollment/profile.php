<?php
    require_once 'session_cookie_check.php';

    // Daca a fost validat si are eroare, o vom folosi
    $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
    
    // Clear session data after displaying
    unset($_SESSION['error']);
    
    $table = 'users';
    $column_id = 'user_id';

    $query = "SELECT first_name, last_name, email, phone_number, sign_up_date, height, weight, date_of_birth, gender FROM $table WHERE $column_id = ?";
    $stmt = $link->prepare($query);
    
    if (!$stmt) {
        die("Query preparation failed: " . $link->error);
    }

    $height = 0;
    $weight = 0;
    $date_of_birth = "";
    $gender = null;

    $stmt->bind_param("i", $logged_user_id);
    $stmt->execute();

    $stmt->bind_result($first_name, $last_name, $email, $phone, $sign_up_date, $height, $weight, $date_of_birth, $gender);
    $stmt->fetch();
    $stmt->close();


    if ($logged_user_type == 'client'){
        $query3 = "
        SELECT 
            tp.name AS course_name,
            tp.type AS type,
            tp.level AS level,
            CONCAT(u.first_name, ' ', u.last_name) AS trainer_name,
            cp.created_at AS sign_up_date,
            tp.duration - TIMESTAMPDIFF(WEEK, cp.created_at, NOW()) AS weeks_left
        FROM 
            clients_programs cp
        JOIN 
            training_programs tp ON cp.program_id = tp.program_id
        JOIN 
            users u ON tp.trainer_id = u.user_id
        WHERE 
            cp.client_id = ?
        ORDER BY 
            cp.created_at DESC
        ";

        $stmt = $link->prepare($query3);
        $stmt->bind_param("i", $logged_user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }

        // Close the statement
        $stmt->close();

    } elseif ($logged_user_type == 'trainer'){
        // Fetch courses the trainer is teaching
        $query_courses = "
            SELECT 
                tp.program_id,
                tp.name AS course_name,
                tp.type AS type,
                tp.level AS level,
                tp.duration AS duration,
                COUNT(DISTINCT cp.client_id) AS registered_count
            FROM 
                training_programs tp
            LEFT JOIN 
                clients_programs cp ON tp.program_id = cp.program_id
            WHERE 
                tp.trainer_id = ?
            GROUP BY 
                tp.program_id, tp.name, tp.type, tp.level, tp.duration
            ORDER BY 
                tp.name ASC
        ";

        $stmt_courses = $link->prepare($query_courses);
        $stmt_courses->bind_param("i", $logged_user_id);
        $stmt_courses->execute();
        $result_courses = $stmt_courses->get_result();

        $courses = [];
        while ($course_row = $result_courses->fetch_assoc()) {
            // For each course, fetch registered clients
            $program_id = $course_row['program_id'];

            $query_clients = "
                SELECT DISTINCT 
                    u.first_name,
                    u.last_name
                FROM 
                    clients_programs cp
                JOIN 
                    users u ON cp.client_id = u.user_id
                WHERE 
                    cp.program_id = ?
            ";

            $stmt_clients = $link->prepare($query_clients);
            $stmt_clients->bind_param("i", $program_id);
            $stmt_clients->execute();
            $result_clients = $stmt_clients->get_result();

            $clients = [];
            while ($client_row = $result_clients->fetch_assoc()) {
                $clients[] = $client_row;
            }

            $stmt_clients->close();

            // Add course details and registered clients to the course list
            $courses[] = [
                'course_name' => $course_row['course_name'],
                'type' => $course_row['type'],
                'level' => $course_row['level'],
                'duration' => $course_row['duration'],
                'registered_count' => $course_row['registered_count'],
                'clients' => $clients,
            ];
        }

        $stmt_courses->close();

    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" type="text/css" href="style.css?v=1.3">
</head>
<body>   
    <!-- Header Section -->
    <div class="header-container">
        <h1>Welcome to Vlad Gym</h1>
    </div>

    <!-- Navigation Bar -->
    <div class="navbar">
        <div class="center-links">
            <a href="index.php" class="logo">Home</a>
            <a href="training_programs.php">Training Programs</a>
            <a href="about_us.php">About Us</a>
        </div>
        <div class="right-links">
            <a href="logout.php">Log out</a>
        </div>
    </div>

    <!-- Error Message -->
    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <!-- Profile Section -->
    <div class="profile-container">
        <!-- Personal Data Section -->
        <div class="personal-data">
            <h3>Personal Data</h3>
            <form method="post" action="edit_data.php">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" class="updatable" value="<?= htmlspecialchars($first_name) ?>" disabled><br>

                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" class="updatable" name="last_name" value="<?= htmlspecialchars($last_name) ?>" disabled><br>

                <label for="email">Email</label>
                <input type="text" id="email" name="email" value="<?= htmlspecialchars($email) ?>" disabled><br>

                <label for="phone">Phone Number</label>
                <input type="text" id="phone2" name="phone" value="<?= htmlspecialchars($phone) ?>" disabled><br>

                <label for="height">Height</label>
                <input type="text" id="height" class="updatable" name="height" value="<?= htmlspecialchars($height) ?>" disabled><br>

                <label for="weight">Weight</label>
                <input type="text" id="weight" class="updatable" name="weight" value="<?= htmlspecialchars($weight) ?>" disabled><br>

                <label for="date_of_birth">Date of Birth</label>
                <input type="text" id="date_of_birth" class="updatable" name="date_of_birth" value="<?= htmlspecialchars($date_of_birth) ?>" disabled><br>

                <label for="gender">Gender</label>
                <select id="gender" class="updatable" name="gender" disabled>
                    <option value="Male" <?= $gender == 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $gender == 'Female' ? 'selected' : '' ?>>Female</option>
                </select><br>

                <input type="submit" id="submit_profile_change" value="Submit" style="display: none;">
            </form>
            <button id="edit_button">Edit</button>
        </div>

        <?php if ($logged_user_type == 'client') :?>
            <div class="courses-table">
                <h3>Your Training Programs</h3>
                <?php if (empty($courses)) : ?>
                    <div class="no-courses">
                        <p>You aren't registered to any courses.</p>
                    </div>
                <?php else : ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Nr.</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Level</th>
                                <th>Trainer</th>
                                <th>Sign-Up Date</th>
                                <th>Weeks Left</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $index => $course) : ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($course['course_name']) ?></td>
                                    <td><?= htmlspecialchars($course['type']) ?></td>
                                    <td><?= htmlspecialchars($course['level']) ?></td>
                                    <td><?= htmlspecialchars($course['trainer_name']) ?></td>
                                    <td><?= htmlspecialchars(date("Y-m-d", strtotime($course['sign_up_date']))) ?></td>
                                    <td><?= max(0, htmlspecialchars($course['weeks_left'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php elseif ($logged_user_type == 'trainer') :?>
            <div class="courses-table">
                <h3>Courses You Are Teaching</h3>
                <?php if (empty($courses)) : ?>
                    <div class="no-courses">
                        <p>You are not teaching any courses.</p>
                    </div>
                <?php else : ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Nr.</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Level</th>
                                <th>Duration</th>
                                <th>Registered Clients</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $index => $course) : ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($course['course_name']) ?></td>
                                    <td><?= htmlspecialchars($course['type']) ?></td>
                                    <td><?= htmlspecialchars($course['level']) ?></td>
                                    <td><?= htmlspecialchars($course['duration']) ?> weeks</td>
                                    <td>
                                        <?php if (!empty($course['clients'])) : ?>
                                            <ul>
                                                <?php foreach ($course['clients'] as $client) : ?>
                                                    <li><?= htmlspecialchars($client['first_name'] . " " . $client['last_name']) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else : ?>
                                            No clients registered.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div> 
    
    <script src="profile.js"></script>
</body>
</html>
