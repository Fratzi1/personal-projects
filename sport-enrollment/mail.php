<?php

// Include the Composer autoloader
require 'vendor/autoload.php';

function send_email_2fa ($recipient, $two_factor_code){
    // Create a new PHPMailer instance
    $mail = new PHPMailer\PHPMailer\PHPMailer();

    $mail->SMTPDebug = 2; // Set the debug output level (2 is good for detailed output)
    $mail->isSMTP();      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com'; // SMTP server
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->Username = 'vlad.proiectdaw@gmail.com'; // Your email address
    $mail->Password = 'glxz smnu zmez rjsj'; // Your app-specific password
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587; // Port for TLS     

    // Set email format to HTML
    $mail->isHTML(true);

    // Set the recipient and sender
    $mail->setFrom('vlad.proiectdaw@gmail.com', 'Proiect Daw');
    $mail->addAddress($recipient); // Add a recipient

    // Set email subject and body
    $mail->Subject = 'Two Factor Verification';
    $mail->Body    = 'Your verification code is: ' . $two_factor_code . 
                 '. It will expire in 10 minutes. <br><br>'.
                 'Yours sincerely,<br>Proiect Daw Team';

    // Send the email
    if ($mail->send()) {
        echo 'Message has been sent';
    } else {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }
}

function send_email_contact_form ($recipient, $name, $message){
    // Create a new PHPMailer instance
    $mail = new PHPMailer\PHPMailer\PHPMailer();

    $mail->SMTPDebug = 2; // Set the debug output level (2 is good for detailed output)
    $mail->isSMTP();      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com'; // SMTP server
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->Username = 'vlad.proiectdaw@gmail.com'; // Your email address
    $mail->Password = 'glxz smnu zmez rjsj'; // Your app-specific password
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587; // Port for TLS     

    // Set email format to HTML
    $mail->isHTML(true);

    // Set the recipient and sender
    $mail->setFrom('vlad.proiectdaw@gmail.com', 'Proiect Daw Team');
    $mail->addAddress($recipient); // Add a recipient

    // Set email subject and body
    $mail->Subject = 'FAQ message';
    $mail->Body    = 'Hey, ' .$name. '!<br><br>'.
                 'Thanks for your message. The team will come back to you shortly.<br><br>'. 
                 'Your message: <br>'.
                 '<i>'.$message.'</i><br><br>'.
                 'Yours sincerely,<br>Proiect Daw Team';

    // Send the email
    if ($mail->send()) {
        echo 'Message has been sent';
    } else {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }
}
?>
