<?php
session_start();

// Daca sign up a fost corect 
$succes_signup = isset($_SESSION['succes']) ? $_SESSION['succes'] : null;

// Daca a fost validat si are eroare, o vom folosi
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;

// La fel vom utiliza valorile corecte
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];

// Clear session data after displaying
unset($_SESSION['succes']);

// Clear session data after displaying
unset($_SESSION['error']);

unset($_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log-in</title>
    <link rel="stylesheet" type="text/css" href="login.css" />

</head>
<body>
    <?php if ($succes_signup): ?>
        <div class="succes-signup-message"><?= htmlspecialchars($succes_signup) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <div>
        Log-in
    </div>
    <form method="post" action="login_process.php">
        <label for="email">Email</label><br>
        <input type="text" id="email" name="email" required><br>

        <label for="password">Password:</label><br>
        <input type="password" minlength="8" id="password" name="password" required><br>
        <div>
            <a href="forgot_pass.php">Forgot password</a>
        </div>
        <div>
            <input type="checkbox" id="remember_me" name="remember_me"/>
            <label for="remember_me">Remember me</label>
        </div>
        <input type="submit" value="Log in">
    </form>
    <div>
        <p>Not registered? <a href="signup.php">Sign up</a></p>
    </div>
</body>
</html>