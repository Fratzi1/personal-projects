<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?=htmlspecialchars($pageTitle);?> </title>
    <link rel="stylesheet" type="text/css" href="style.css?v=1.7" />
</head>
<body>
    <div class="header-container">
        <h1>Welcome to Vlad Gym</h1>
    </div>
    
    <div class="navbar">
    
        <!-- Navigation links -->
        <div class="center-links">
            <a href="index.php" class="logo">Home</a>
            <a href="training_programs.php">Training programs</a>
            <?php if($logged_user_type == 'client') :?>
                <a href="cooper_test.php">Cooper Test</a>
            <?php endif; ?>
            <a href="about_us.php">About Us</a>
        </div>

        <div class="right-links">
            <!-- Cazul I: e logat cineva prin datele dintr-o sesiune => apare Profile si Logout -->
            <?php if ($logged_user_id !== null) : ?>
                <a href="profile.php">My profile</a>
                <a href="logout.php">Log out</a>
            
            <!-- cazul II: nu e logat nimeni => apare Login si Signup -->    
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign Up</a>
            <?php endif; ?>
        </div>
            
    </div>