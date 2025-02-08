<!-- Parsare date -->
<?php
    require_once 'session_cookie_check.php';

    function fetchFullHtml($url) {
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept-Language: en-US,en;q=0.9',
            'Connection: keep-alive'
        ]);
    
        // Follow redirects if there are any
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
        // Set a timeout (e.g., 30 seconds)
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
        // Optionally disable SSL verification (not recommended for production)
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
        $html = curl_exec($ch);
    
        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
            curl_close($ch);
            return false;
        }
    
        curl_close($ch);
        return $html;
    }
    
    function extractTestForm($html) {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true); // Suppress errors for invalid HTML
        $dom->loadHTML($html);
        libxml_clear_errors();
    
        $xpath = new DOMXPath($dom);
        
        // Find the <section> with id "test-form"
        $testForm = $xpath->query('//*[@id="test-form"]');
        
        if ($testForm->length > 0) {
            // Remove the <p> element inside <fieldset id="age-sex-details">
            $paragraph = $xpath->query('//*[@id="age-sex-details"]/p');
            if ($paragraph->length > 0) {
                $paragraph->item(0)->parentNode->removeChild($paragraph->item(0));
            }
    
            // Return the updated HTML of the <section> element
            return $dom->saveHTML($testForm->item(0));
        }
    
        return null; // Return null if the <section> is not found
    }
    

    function populateFields($formHtml, $sex, $age) {
        // Handle the "sex" radio buttons
        if ($sex === 'Male') {
        
            // Ensure "Male" is checked and "Female" is unchecked
            $formHtml = preg_replace('/<input[^>]*id="male"[^>]*checked="checked"/', '<input type="radio" name="sex" id="male" value="male" checked="checked"', $formHtml);
            $formHtml = preg_replace('/<input[^>]*id="female"[^>]*checked="checked"/', '<input type="radio" name="sex" id="female" value="female"', $formHtml);
        
        } elseif ($sex === 'Female') {
        
            // Ensure "Female" is checked and "Male" is unchecked
            $formHtml = preg_replace('/<input[^>]*id="male"[^>]*checked="checked"/', '<input type="radio" name="sex" id="male" value="male"', $formHtml);
            $formHtml = preg_replace('/<input[^>]*id="female"/', '<input type="radio" name="sex" id="female" value="female" checked="checked"', $formHtml);
        
        }
    
        // Handle the "age" input field
        $formHtml = preg_replace('/<input[^>]*name="age"[^>]*value="[^"]*"/', '<input name="age" value="' . htmlspecialchars($age) . '"', $formHtml);
        
        $formHtml = str_replace(
            'action="https://runbundle.com/tools/vo2-max-calculators/cooper-test"',
            'action="cooper_test.php"',
            $formHtml
        );
        
    
        return $formHtml;
    }

    function getUserData($link, $logged_user_id){
        $table = 'users';
        $column_id = 'user_id';

        $query = "SELECT date_of_birth, gender FROM $table WHERE $column_id = ?";
        $stmt = $link->prepare($query);
        
        if (!$stmt) {
            die("Query preparation failed: " . $link->error);
        }

        $date_of_birth = null;
        $gender = null;

        $stmt->bind_param("i", $logged_user_id);
        $stmt->execute();

        $stmt->bind_result($date_of_birth, $gender);
        $stmt->fetch();
        $stmt->close();

        return [$date_of_birth, $gender];
    }

    function calculateAge($date_of_birth) {
        $dob = new DateTime($date_of_birth); // Create a DateTime object for the date of birth
        $now = new DateTime(); // Current date
        $age = $dob->diff($now)->y; // Difference in years
        return $age;
    }

    function calculateVO2MaxAndLevel($age, $gender, $distance) {
        $age = (int)$age;
        $distance = (int)$distance;
        $gender = strtolower(trim($gender));
    
        $vo2max = ($distance - 504.9) / 44.73;
        $level = "Unknown";

        if ($gender === "male") {
            if ($age >= 20 && $age <= 29) {
                if ($distance < 1600) $level = "Very poor";
                elseif ($distance < 2200) $level = "Poor";
                elseif ($distance < 2400) $level = "Average";
                elseif ($distance <= 2800) $level = "Above average";
                else $level = "Excellent";
            } elseif ($age >= 30 && $age <= 39) {
                if ($distance < 1500) $level = "Very poor";
                elseif ($distance < 1900) $level = "Poor";
                elseif ($distance < 2300) $level = "Average";
                elseif ($distance <= 2700) $level = "Above average";
                else $level = "Excellent";
            } elseif ($age >= 40 && $age <= 49) {
                if ($distance < 1400) $level = "Very poor";
                elseif ($distance < 1700) $level = "Poor";
                elseif ($distance < 2100) $level = "Average";
                elseif ($distance <= 2500) $level = "Above average";
                else $level = "Excellent";
            } elseif ($age >= 50) {
                if ($distance < 1300) $level = "Very poor";
                elseif ($distance < 1600) $level = "Poor";
                elseif ($distance < 2000) $level = "Average";
                elseif ($distance <= 2400) $level = "Above average";
                else $level = "Excellent";
            }
        } elseif ($gender === "female") {
            if ($age >= 20 && $age <= 29) {
                if ($distance < 1500) $level = "Very poor";
                elseif ($distance < 1800) $level = "Poor";
                elseif ($distance < 2200) $level = "Average";
                elseif ($distance <= 2700) $level = "Above average";
                else $level = "Excellent";
            } elseif ($age >= 30 && $age <= 39) {
                if ($distance < 1400) $level = "Very poor";
                elseif ($distance < 1700) $level = "Poor";
                elseif ($distance < 2000) $level = "Average";
                elseif ($distance <= 2500) $level = "Above average";
                else $level = "Excellent";
            } elseif ($age >= 40 && $age <= 49) {
                if ($distance < 1200) $level = "Very poor";
                elseif ($distance < 1500) $level = "Poor";
                elseif ($distance < 1900) $level = "Average";
                elseif ($distance <= 2300) $level = "Above average";
                else $level = "Excellent";
            } elseif ($age >= 50) {
                if ($distance < 1100) $level = "Very poor";
                elseif ($distance < 1400) $level = "Poor";
                elseif ($distance < 1700) $level = "Average";
                elseif ($distance <= 2200) $level = "Above average";
                else $level = "Excellent";
            }
        }

        return [$vo2max, $level];
        }

    

    $pageTitle = "Coooper Test";
    include 'header.php';
?>

<?php if ($logged_user_id === null) : ?>
        <div class="login-message">
                You need to log in order to able to do Cooper Test.
        </div>
<?php elseif ($logged_user_type === 'client') : ?>
        <head>
            <link rel="stylesheet" href="scrapping.css?v=1.0">
        </head>
        <div class="wrapper">
            <?php    
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    
                    $distance = $_POST['distance'];
                    $age = $_POST['age'];
                    $gender = $_POST['sex'];
                    
                    list($vo2max, $level) = calculateVO2MaxAndLevel($age, $gender, $distance);

                    $_SESSION['age'] = $age;
                    $_SESSION['distance'] = $distance;
                    $_SESSION['sex'] = $gender;
                    $_SESSION['vo2max'] = number_format($vo2max,2);
                    $_SESSION['level'] = $level;

            ?>
            <div class="result-message">
                <p>Your VO2max: <strong><?= number_format($vo2max,2)?></strong></p>
                <p>Level: <strong><?= $level ?></strong></p>
                <p>You can download your full report from <a href="cooper_report.php">here.</a></p>
            </div>
                    <?php
                } else {
                    $url = 'https://runbundle.com/tools/vo2-max-calculators/cooper-test';
                    $html = fetchFullHtml($url);

                    // Extract the test-form
                    $testForm = extractTestForm($html);

                    if ($testForm) {
                        // Fetch data from your database
                        
                        list($date_of_birth, $gender) = getUserData($link, $logged_user_id);
                        
                        $sex = $gender;
                        $age = calculateAge($date_of_birth);
                    
                        // Populate the form fields
                        $populatedForm = populateFields($testForm, $sex, $age);
                    
                        // Output the modified form
                        echo $populatedForm;
                    } else {
                        echo "Form not found.";
                    }
                }
            ?>   
        </div>
    </body>
    </html>
<?php endif; ?>

  


    

