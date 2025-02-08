<?php
    session_start();
    require_once 'db_connect.php';
    
    $logged_user_id = null;
    $logged_user_type = null;

    $timeout_duration = 30 * 60; // 30 minutes

    // Check if the user is logged in via session
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
        
        // Check if the session has expired due to inactivity
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
            // Destroy session if timeout is exceeded
            session_unset();
            session_destroy();
            header("Location: login.php");
            exit;
        }

        // Update last activity time
        $_SESSION['last_activity'] = time();
        
        // User is already logged in via session
        $logged_user_id = $_SESSION['user_id'];
        $logged_user_type = $_SESSION['user_type'];

    } elseif (isset($_COOKIE['remember_token'])) {
        
        // Session is not set, check the "Remember Me" cookie
        $remember_token = $_COOKIE['remember_token'];
        
        $table ='login_cookie_tokens';

        $query = "SELECT user_id, user_type FROM $table WHERE token = ?";
        $stmt = $link->prepare($query);
        $stmt->bind_param("s", $remember_token);
        $stmt->execute();
        $stmt->bind_result($user_id, $user_type);

            if ($stmt->fetch()) {
                // Valid token, re-establish session
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_type'] = $user_type;
                $_SESSION['last_activity'] = time();  // Set last activity time
                $logged_user_id = $user_id;
                $logged_user_type = $user_type;
            }
            $stmt->close();

        if (!isset($logged_user_id)) {
            // Invalid token; clear the cookie
            setcookie('remember_token', '', time() - 3600, "/", "", true, true);
        }
    }
?>