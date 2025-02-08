<?php
    
    session_start();
    require_once 'redirect_functions.php';
    // Daca a fost validat si are eroare, o vom folosi
    $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
    // Clear session data after displaying
    unset($_SESSION['error']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $input_code = $_POST['2fa_code'];
        if ($input_code == $_SESSION['2fa_code']) {
            redirect_with_succes("You can now enter new password.", 'reset_password.php');
        }
        redirect_with_error("Invalid 2FA code. Please try again.", 'forgot_pass_process.php');
    }
    
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify 2F code</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<form method="POST" action="forgot_pass_process.php">
    <label for="2fa_code">Enter 2FA Code</label>
    <input type="text" name="2fa_code" id="2fa_code" required />
    <button type="submit">Verify</button>
</form>
    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
</body>
</html>