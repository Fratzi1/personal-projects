<?php

session_start();
require_once 'db_connect.php';
require_once 'redirect_functions.php';
require_once 'mail.php';

// Daca a fost validat si are eroare, o vom folosi
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
// Clear session data after displaying
unset($_SESSION['error']);

// Cand da submit se verifica daca mailul e bun
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $_SESSION['email'] = $email;
    // Check required fields
    if (empty($email)) {
        redirect_with_error("Enter email.", 'forgot_pass.php');
    }   
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirect_with_error("Invalid email address.", 'forgot_pass.php');
    }
    
    // Verificare daca email e in db
    $query = "SELECT COUNT(*) FROM users WHERE email = ?";
        
    $stmt = $link->prepare($query);
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    // Get the result
    $stmt->bind_result($count);
    $stmt->fetch();
    
    $stmt->close();  // Close the prepared statement
        
    // Check if the email exists - if it doesn't redirect back to sign up page
    if ($count == 0) {
        $link->close();  // Close the database connection
        redirect_with_error("Email not registered. You have to sign-up first.",'forgot_pass.php');
    }
            
    // Random nr intre cele 2 valori
    $two_factor_code = rand(100000, 999999);
        
    // Save the 2FA code in the session or database temporarily
    $_SESSION['2fa_code'] = $two_factor_code;    
    
    // send email
    send_email_2fa($email, $two_factor_code);
    
    redirect_with_succes("Email sent.", 'forgot_pass_process.php');
    
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form method="post" action="forgot_pass.php">
        <label for="email">Email</label><br>
        <input type="text" id="email" name="email" required><br>
        <input type="submit" value="Send code">
    </form>
    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
</body>
</html>