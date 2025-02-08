<!-- Afisez un formular cu o noua parola + reconfirm -->
<?php
session_start();

// Daca a fost validat si are eroare, o vom folosi
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;

// Clear session data after displaying
unset($_SESSION['error']);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change password</title>
    <link rel="stylesheet" href="style.css?">
</head>
<body>
    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <div>
        Change password
    </div>
    <form method="post" action="reset_password_process.php">
        <label for="password">New Password:</label><br>
        <input type="password" minlength="8" id="password" name="password" required><br>

        <label for="confirm_password">Confirm New Password:</label><br>
        <input type="password" minlength="8" id="confirm_password" name="confirm_password" required><br>
        
        <input type="submit" value="Submit">
    </form>
    <p>*All fields are mandatory</p>
</body>
</html>