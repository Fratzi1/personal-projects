<?php
session_start();
require_once 'redirect_functions.php';
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_code = $_POST['2fa_code'];

    // Check if the entered 2FA code matches the one in session
    if ($input_code == $_SESSION['2fa_code']) {
        // Retrieve user data from the cookie
        if (isset($_COOKIE['temp_user_data'])) {
            $user_data = json_decode(base64_decode($_COOKIE['temp_user_data']), true);
            
            // Save user data into the database
            $query = "INSERT INTO users (first_name, last_name, user_type, email, password, phone_number, sign_up_date)
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $link->prepare($query);
            $stmt->bind_param("sssssss", $user_data['first_name'], $user_data['last_name'], $user_data['user_type'], 
                             $user_data['email'], $user_data['password'], $user_data['phone'], $user_data['sign_up_date']);
            $stmt->execute();
            $stmt->close();
            $link->close();

            // Redirect to login page after successful registration
            setcookie("temp_user_data", "", time() - 3600, "/");  // Clear cookie
            redirect_with_succes("Sign up successful. You can now log in.", 'login.php');
        }
    } else {
        setcookie("temp_user_data", "", time() - 3600, "/");  // Clear cookie
        redirect_with_error("Invalid 2FA code. Please try again.", 'verify_2fa.php');
    }
}
?>

<form method="POST" action="2fa_process.php">
    <label for="2fa_code">Enter 2FA Code</label>
    <input type="text" name="2fa_code" id="2fa_code" required />
    <button type="submit">Verify</button>
</form>
