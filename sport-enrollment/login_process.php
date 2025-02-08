<?php
    session_start();
    require_once 'db_connect.php';
    require_once 'redirect_functions.php';
    require_once 'authentification_functions.php';

    // Tine minte in sesiune datele din formular, cu exceptia parolei
    $_SESSION['form_data'] = $_POST;
    unset($_SESSION['form_data']['password']);

    $email = trim($_POST['email']);
    $input_password = trim($_POST['password']);

    // cazul I: exista campuri goale
    if (empty($email) || empty($input_password)) {
        redirect_with_error("No email and/or password", 'login.php');
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        unset($_SESSION['form_data']['email']);
        redirect_with_error("Invalid email address.",'login.php');
    }

    $table = 'users';    
    $result = check_credentials($link, $table, $email, $input_password);
    if ($result['user_id'] === false) {
        // Daca parola nu e buna, mesaj
        redirect_with_error("Incorrect password. Try again.", 'login.php');
    
    } elseif ($result['user_id'] !== null) {
            
        // Retrieve both user_id and user_type. Store them in a session
        $user_id = $result['user_id'];
        $user_type = $result['user_type']; 
    
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_type'] = $user_type;

        // Daca apasa remember me => pornesc mecanism de cookie
        if (isset($_POST['remember_me'])) { 
            check_token($link, 'login_cookie_tokens', $user_id, $user_type);
        }
            
        if ($user_type == 'admin'){
            $host  = $_SERVER['HTTP_HOST'];
            $extra = "users_admin.php";
            header("Location: http://$host/$extra");
            exit;
        }
        
        // Redirect to the main page
        $host  = $_SERVER['HTTP_HOST'];
        $extra = "index.php";
        header("Location: http://$host/$extra");
        exit;
    }
    
    // Daca nu am gasit mailul
    redirect_with_error("Email not registered. Go and sign up.", 'login.php');
    
    $link->close();
?>