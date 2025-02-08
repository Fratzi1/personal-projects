<?php

    session_start();
    require_once 'db_connect.php';
    require_once 'redirect_functions.php';

    // Sterge parolele
    unset($_SESSION['form_data']['password']);
    unset($_SESSION['form_data']['confirm_password']);

    $input_password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    

    // Check required fields
    if (empty($input_password) || empty($confirm_password)) {
        redirect_with_error("All fields are required.", 'reset_password.php');
    }   

    // Nu e parola valida
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
    if (!(preg_match($pattern, $input_password))) {
        redirect_with_error("Password must be at least 8 characters long. Must include lower case, upper case, number and special characters", 'reset_password.php');
    }

    // Nu corespund parolele
    if ($input_password !== $confirm_password) {
        redirect_with_error("Passwords do not match.",'reset_password.php');
    }


    // If passwords correct, encrypt, change in db and then redirect to login

    $new_password = password_hash($input_password, PASSWORD_DEFAULT);

    $email = $_SESSION['email'];
    unset($_SESSION['email']);

    $query = "UPDATE users SET password=? WHERE email = ?";
    
    $stmt = $link->prepare($query);

    $stmt->bind_param("ss",$new_password, $email);
    $stmt->execute();

    $stmt->close();
    $link->close();
    
    redirect_with_succes("Password updated. You can now log in.", 'login.php');
?>
