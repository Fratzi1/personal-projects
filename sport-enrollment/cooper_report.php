<?php

require_once 'session_cookie_check.php';

define('FPDF_FONTPATH', dirname(__FILE__) . '/font/');

require 'fpdf.php';

// Custom PDF class with footer for page number
class CooperTestPDF extends FPDF {
    // Footer method
    function Footer() {
        // Position at 1.5 cm from the bottom
        $this->SetY(-15);
        // Set font for the footer
        $this->SetFont('Arial', 'I', 10);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

function generateCooperTestPDF($firstName, $lastName, $age, $gender, $time, $score, $vo2max, $level) {
    // Create an instance of the custom PDF class
    $pdf = new CooperTestPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Title
    $pdf->Cell(0, 10, 'Cooper Test Report', 0, 1, 'C');
    $pdf->Ln(10);

    // User information
    $pdf->SetFont('Arial', '', 12);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'First Name:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $firstName, 0, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Last Name:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $lastName, 0, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Age:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $age, 0, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Gender:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $gender, 0, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Time:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, "$time minutes", 0, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Your Score:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $score, 0, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Your VO2Max:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $vo2max, 0, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Your Level:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $level, 0, 1);

    // Add today's date beneath the level
    $pdf->Ln(5);
    $todayDate = date('F j, Y');
    $pdf->Cell(0, 10, "Date: $todayDate", 0, 1);

    // Explanation text
    $pdf->Ln(10);
    $text = "The Cooper test is a simple way to estimate a runner's VO2 Max, created by Dr. Kenneth Cooper and featured in his book, Aerobics. The test involves running for 12 minutes and recording the distance covered. For best results, the test should be done on a track under moderate weather conditions, with proper warm-up beforehand. The distance is then used to estimate VO2 Max and assess performance based on age and gender. The Cooper test is popular due to its simplicity, efficiency, and repeatability, making it a reliable tool for monitoring progress.";
    
    $pdf->MultiCell(0, 10, $text);

    // Output the PDF
    $fileName = 'cooper_test_report.pdf';
    $pdf->Output('D', $fileName); // 'D' forces the download
}

function getUserData($link, $logged_user_id){
    $table = 'users';
    $column_id = 'user_id';

    $query = "SELECT first_name, last_name FROM $table WHERE $column_id = ?";
    $stmt = $link->prepare($query);
    
    if (!$stmt) {
        die("Query preparation failed: " . $link->error);
    }

    $date_of_birth = null;
    $gender = null;

    $stmt->bind_param("i", $logged_user_id);
    $stmt->execute();

    $first_name = '';
    $last_name = '';

    $stmt->bind_result($first_name, $last_name);
    $stmt->fetch();
    $stmt->close();

    return [$first_name, $last_name];
}

list($first_name, $last_name) = getUserData($link, $logged_user_id);

$firstName = $first_name;
$lastName = $last_name;
$age = $_SESSION['age'];
$gender = $_SESSION['sex'];
$time = 12; // Minutes
$score = $_SESSION['distance']; // Meters
$vo2max = $_SESSION['vo2max']; // Meters
$level = $_SESSION['level'];

unset($_SESSION['age']);
unset($_SESSION['distance']);
unset($_SESSION['sex']);
unset($_SESSION['vo2max']);
unset($_SESSION['level']);

// Generate the PDF
generateCooperTestPDF($firstName, $lastName, $age, $gender, $time, $score, $vo2max, $level);
?>