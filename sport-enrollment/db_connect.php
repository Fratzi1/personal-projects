<?php

    $server = 'nwhazdrp7hdpd4a4.cbetxkdyhwsb.us-east-1.rds.amazonaws.com';
    $username = 'snlf1e75emr6xu8z';
    $password = 'xaysrwe724xapov3';
    $database = 'ze4xiva4lm9mohzr';
    $port = 3306;

    // Sintaxa: mysqli_connect(host, user,pass, database_name)
    $link = new mysqli($server, $username, $password, $database, $port);

    if(!$link){
        echo ('Error!unable to connect to MySql!');
        exit;
    } else {
        // echo('Succesfully connected to Mysql');
    }
?>