<?php

session_start();
require_once 'db_connect.php';
require_once 'mail.php';
require_once 'redirect_functions.php';

// Checking valid form is submitted or not
if (isset($_POST['submit_btn'])) {
    
    $_SESSION['form_data'] = $_POST;

    $name = trim($_POST['name']);
    $message = trim($_POST['message']);
    $email = trim($_POST['email']);

    if (empty($name) || empty($message) || empty($email)){
        redirect_with_error("All fields are required.", 'index.php');
    }   

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        unset($_SESSION['form_data']['email']);
        redirect_with_error("Invalid email address.", 'index.php');
    }
  
    // Storing google recaptcha response
    // in $recaptcha variable
    $recaptcha = $_POST['g-recaptcha-response'];

    // Put secret key here, which we get
    // from google console
    $secret_key = '6Led6JwqAAAAACdd9heJ8MzyCcM1cc3-2bUp9Vzs';

    // Hitting request to the URL, Google will
    // respond with success or error scenario
    $url = 'https://www.google.com/recaptcha/api/siteverify?secret='
          . $secret_key . '&response=' . $recaptcha;

    // Making request to verify captcha
    $response = file_get_contents($url);

    // Response return by google is in
    // JSON format, so we have to parse
    // that json
    $response = json_decode($response);

    // Checking, if response is true or not
    if ($response->success == true) {
        
        unset($_SESSION['form_data']);
        
        // Store message in db
        $query = "INSERT INTO contact_form_submissions (name, email, message) VALUES (?, ?, ?)";

        $stmt = $link->prepare($query);
        $stmt->bind_param("sss", $name, $email, $message);

        $stmt->execute();

        $stmt->close();
        $link->close();

        // Give confirmation email
        send_email_contact_form ($email, $name, $message);

        // Redirect to the main page
        $host  = $_SERVER['HTTP_HOST'];
        $extra = "index.php";
        header("Location: http://$host/$extra");
        exit;

    } else {
        echo '<script>alert("Error in Google reCAPTACHA")</script>';
    }
}

?>