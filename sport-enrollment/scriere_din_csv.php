
<?php
    require_once('db_connect.php');
        
    // Functie care preia un fisier si il deschide
    function getCSVContent($filename) {
        // Functii care verifica daca exista fisierul si apoi copiaza continutul intr un string  
        if (file_exists($filename)) {
            return file_get_contents($filename);
        }
    }

    // Aici vin datele din fisier
    $csv_data = getCSVContent('country_telephones.csv');
        
    // Convert CSV string to array
    $lines = explode("\n", $csv_data);
        
    for ($i = 0; $i < count($lines); $i++) {
        if (!empty($lines[$i])) {
            // Imparte continutul din $lines in 2 variabile, prescurtare si numele judetului
            list($country, $code) = explode(",", $lines[$i]);
            $query = "INSERT INTO country_phone_codes (country,code) VALUES ('$country','$code');";
            $link->query($query);
        }
    }
    mysqli_close($link);
?>