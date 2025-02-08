<?php

    session_start();
    require_once 'db_connect.php';
    require_once 'redirect_functions.php';
    require_once 'mail.php';

    // Tine minte in sesiune datele din formular, cu exceptia parolelor
    $_SESSION['form_data'] = $_POST;
    unset($_SESSION['form_data']['password']);
    unset($_SESSION['form_data']['confirm_password']);


    // Sanitize and validate inputs
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    
    $email = trim($_POST['email']);

    $input_password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    $phone_prefix = $_POST['country_prefix'];
    $phone_number = $_POST['phone'];
    
    $user_type = $_POST['user_type'];

    // Check required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($input_password) || empty($confirm_password) || empty($phone_prefix) || empty($phone_number) || empty($user_type)) {
        redirect_with_error("All fields are required.", 'signup.php');
    }   

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        unset($_SESSION['form_data']['email']);
        redirect_with_error("Invalid email address.", 'signup.php');
    }

    // Nu e parola valida
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
    if (!(preg_match($pattern, $input_password))) {
        redirect_with_error("Password must be at least 8 characters long. Must include lower case, upper case, number and special characters", 'signup.php');
    }

    // Nu corespund parolele
    if ($input_password !== $confirm_password) {
        redirect_with_error("Passwords do not match.",'signup.php');
    }

    // Validate phone number (e.g., numeric and length)
    if (!(is_numeric($phone_number)) || strlen($phone_number) > 9) {
        unset($_SESSION['form_data']['phone']);
        redirect_with_error("Invalid phone number.",'signup.php');
    }
    
    $phone = $phone_prefix . $phone_number;
    $table = 'users';

    $query = "SELECT COUNT(*) FROM $table WHERE email = ?";
    
    $stmt = $link->prepare($query);

    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Get the result
    $stmt->bind_result($count);
    $stmt->fetch();

    $stmt->close();  // Close the prepared statement
    
    // Check if the email exists - if it does redirect back to sign up page
    if ($count > 0) {
        $link->close();  // Close the database connection
        redirect_with_error("Email already registerd.",'signup.php');
    }
        
    // Random nr intre cele 2 valori
    $two_factor_code = rand(100000, 999999);
    
    // Save the 2FA code in the session or database temporarily
    $_SESSION['2fa_code'] = $two_factor_code;    
    
    // send email
    send_email_2fa($email, $two_factor_code);

    // Store the user data temporarily in a cookie (base64 encoded to keep it secure)
    $user_data = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'user_type' => $user_type,
        'email' => $email,
        'password' => password_hash($input_password, PASSWORD_DEFAULT),
        'phone' => $phone,
        'sign_up_date' => date('Y-m-d')
    ];

    setcookie("temp_user_data", base64_encode(json_encode($user_data)), time() + 600, "/");  // 10 minutes expiration
    
    // Redirect to 2FA verification page
    redirect_with_succes("Please check your email for the 2FA code.", '2fa_process.php');  
?>
