<?php
    require_once 'session_cookie_check.php';

    function getTrainerName($link,$trainer_id) {
        $query = "SELECT first_name, last_name FROM users WHERE user_id=?";
        $stmt = $link->prepare($query);
        
        if (!$stmt) {
            die("Query preparation failed: " . $link->error);
        }

        $stmt->bind_param("i", $trainer_id);
        $stmt->execute();

        $first_name = '';
        $last_name = '';

        $stmt->bind_result($first_name, $last_name);
        $stmt->fetch();
        $stmt->close();

        return [$first_name, $last_name];
    }


    // Aduce din db date despre programe de antrenament
    $query = "SELECT program_id, name, type, duration, level, price, trainer_id FROM training_programs";
    $stmt = $link->prepare($query);
    
    if (!$stmt) {
        die("Query preparation failed: " . $link->error);
    }

    $stmt->execute();

    $stmt->bind_result($program_id, $name, $type, $duration, $level, $price, $trainer_id);
    
    $trainingPrograms = [];

    // Fetch all results and store them in the array
    while ($stmt->fetch()) {
        $trainingPrograms[] = [
            'program_id' => $program_id,
            'name' => $name,
            'type' => $type,
            'duration' => $duration,
            'level' => $level,
            'price' => $price,
            'trainer_id' =>$trainer_id
        ];
    }

    $stmt->close();

    function checkClientProgram($link, $client_id, $program_id) {
        $query = "SELECT COUNT(*) FROM clients_programs WHERE client_id = ? AND program_id = ?";
        $count = 0;
        $stmt = $link->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ii", $client_id, $program_id);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
            return $count > 0;
        }
        return false;
    }

    function isTrainerRequestPending($link, $trainer_id, $course_id) {
        // Query to check if there is a pending application for the trainer and the course
        $count = 0;
        $query = "SELECT COUNT(*) FROM trainer_course_requests WHERE trainer_id = ? AND course_id = ? AND status = 'pending'";
        $stmt = $link->prepare($query);
        if (!$stmt) {
            die("Query preparation failed: " . $link->error);
        }
        $stmt->bind_param("ii", $trainer_id, $course_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    
        if ($count > 0) {
            return true;
        }
        return false;
    }
    

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['program_id']) && $logged_user_type == 'client') {
        $program_id = (int)$_POST['program_id'];

        // Check if the user is already registered for the program
        $isRegistered = checkClientProgram($link, $logged_user_id, $program_id);

        if (!$isRegistered) {
            // Register the user for the selected program
            $query = "INSERT INTO clients_programs (client_id, program_id) VALUES (?, ?)";
            $stmt = $link->prepare($query);
            if ($stmt) {
                $stmt->bind_param("ii", $logged_user_id, $program_id);
                $stmt->execute();
                $stmt->close();
            }
        }
        // Redirect to the same page to avoid duplicate form submissions
        header("Location: training_programs.php");
        exit;

    } 
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['program_id']) && $logged_user_type == 'trainer') {
        
        $program_id = (int)$_POST['program_id'];

        // Check if the trainer has already applied for this course
        if (isTrainerRequestPending($link, $logged_user_id, $program_id) == false) {
            // Insert the application into the trainer_course_requests table with a 'pending' status
            $query = "INSERT INTO trainer_course_requests (trainer_id, course_id, status) VALUES (?, ?, 'pending')";
            $stmt = $link->prepare($query);
            if ($stmt) {
                $stmt->bind_param("ii", $logged_user_id, $program_id);
                $stmt->execute();
                $stmt->close();
            }
        }

        // Redirect to avoid resubmission
        header("Location: training_programs.php");
        exit;
    }

    $pageTitle = "Training programs";
    $user_type = $logged_user_type;

    // Contine titlu + navbar
    include 'header.php';
?>
      
    </div>

    <!-- If no one is logged => you cannot register or teach course  -->
    <?php if ($logged_user_id === null) : ?>
                <div class="login-message">
                    You need to log in order to able to register for courses.
                </div>

    <!-- Daca e user => add to cart -->
    <?php elseif ($logged_user_type == 'client'): ?>
        
        <div class="programs-container">
            <?php foreach ($trainingPrograms as $program) : ?>
                <?php if ($program['trainer_id']): ?>
                    <?php
                         // nume si prenume antrenor
                        list($first_name, $last_name) = getTrainerName($link, $program['trainer_id']);
                        $isRegistered = checkClientProgram($link, $logged_user_id, $program['program_id']);
                    ?>
                    <div class="program-card">
                        <h3><?= htmlspecialchars($program['name']) ?></h3>
                        <p>Type: <?= htmlspecialchars($program['type']) ?></p>
                        <p>Duration: <?= htmlspecialchars($program['duration']) ?> weeks</p>
                        <p>Level: <?= htmlspecialchars($program['level']) ?></p>
                        <p>Price: $<?= htmlspecialchars($program['price']) ?></p>
                        <p>Trainer: <?= htmlspecialchars($first_name." ".$last_name) ?></p>
                        
                        <form method="post" action="training_programs.php">
                                <input type="hidden" name="program_id" value="<?= htmlspecialchars($program['program_id']) ?>">
                                <?php if ($isRegistered) : ?>
                                    <button class="add-to-cart-btn added" disabled>Registered</button>
                                <?php else : ?>
                                    <button class="add-to-cart-btn">Register</button>
                                <?php endif; ?>
                            </form>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    
    <?php elseif ($logged_user_type == 'trainer'): ?>
        
        <div class="programs-container">
            <?php foreach ($trainingPrograms as $program) : ?>
                <?php

                    // nume si prenume antrenor
                    list($first_name, $last_name) = getTrainerName($link, $program['trainer_id']);
                    if ($first_name){
                        $full_name = " ".$first_name." ".$last_name;
                    } else {
                        $full_name = " Not assigned";
                    }
                   
                    $trainerRequestPending = isTrainerRequestPending($link, $logged_user_id, $program['program_id']);
                
                ?>
                <div class="program-card">
                    <h3><?= htmlspecialchars($program['name']) ?></h3>
                    <p>Type: <?= htmlspecialchars($program['type']) ?></p>
                    <p>Duration: <?= htmlspecialchars($program['duration']) ?> weeks</p>
                    <p>Level: <?= htmlspecialchars($program['level']) ?></p>
                    <p>Price: $<?= htmlspecialchars($program['price']) ?></p>
                    <p>Trainer:<?= htmlspecialchars($full_name) ?></p>
        
                    <!-- Case 1: Check if the course has no trainer assigned -->
                    <?php if ($program['trainer_id'] === null) : ?>
                        <!-- Case 1: Apply as Trainer button -->
                        <form method="post" action="training_programs.php">
                            <input type="hidden" name="program_id" value="<?= htmlspecialchars($program['program_id']) ?>">
                            <button class="add-to-cart-btn" id="apply-as-trainer-<?= htmlspecialchars($program['program_id']) ?>">Apply as Trainer</button>
                        </form>
                    
                    <!-- Case 3: Check if the course already has a trainer assigned -->
                    <?php elseif ($program['trainer_id'] !== null && ($trainerRequestPending == false)) : ?>
                        <button class="add-to-cart-btn assigned" disabled>Already Assigned</button>
                    
                        <!-- Case 2: Check if the trainer has a pending request for the course -->
                    <?php elseif ($trainerRequestPending) : ?>
                        <button class="add-to-cart-btn pending" disabled>Request Pending</button>

                    
                    
                    
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>            
        </div>
    <?php endif; ?>
    <?php 
        $link->close();
    ?>
</div>

</body>
</html>