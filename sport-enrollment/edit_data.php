<?php
    session_start();
    require_once 'db_connect.php';
    require_once 'redirect_functions.php';

    // Verific daca cele 2 campuri sunt completate 
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    
    $height = trim($_POST['height']);
    $weight = trim($_POST['weight']);
    $date_of_birth = trim($_POST['date_of_birth']);
    $gender = trim($_POST['gender']);


    if (empty($first_name) || empty($last_name) || empty($height) || empty($weight) || empty("Phone number")) {
        redirect_with_error("Updated fields cannot be blank!", 'profile.php');
    }

    if (!(ctype_alpha($first_name)) || !(ctype_alpha($last_name))) {
        redirect_with_error("Last name and/or first name must contain only letters of the alphabet!", 'profile.php');
    }

    // Verificare daca inaltimea si greutatea sunt numerice
    if (!is_numeric($height) || !is_numeric($weight)) {
        redirect_with_error("Height and weight must be numerical values!", 'profile.php');
    }

    // Verificare daca data de nastere este in formatul corect (YYYY-MM-DD) si mai mica decat data curenta
    $dob_format = 'Y-m-d';  // Formatul dorit pentru data de nastere
    $date_of_birth_obj = DateTime::createFromFormat($dob_format, $date_of_birth);

    // Verificare daca data de nastere este valida si mai mica decat data curenta
    if (!$date_of_birth_obj || $date_of_birth_obj->format($dob_format) !== $date_of_birth || $date_of_birth_obj > new DateTime()) {
        redirect_with_error("Date of birth must be in the format YYYY-MM-DD and must be a past date!", 'profile.php');
    }

    // Obtin datele de utilzator si fac insert
    $user_id = $_SESSION['user_id'];
    $table = 'users';
    $column_id = substr($table, 0, -1) . '_id';

    $query = "UPDATE $table SET first_name=?, last_name=?, height=?, weight=?, date_of_birth=?, gender=? WHERE $column_id =?";
    $stmt = $link->prepare($query);
        
    if (!$stmt) {
        die("Query preparation failed: " . $link->error);
    }

    $stmt->bind_param("ssiissi", $first_name, $last_name, $height, $weight,$date_of_birth, $gender, $user_id);
    $stmt->execute();

    $stmt->close();
    $link->close();

    // Redirect to profile page
    $host  = $_SERVER['HTTP_HOST'];
    $extra = "profile.php";
    header("Location: http://$host/$extra");
    exit;


?>