<?php

// Functie care trimite prin intermediul unei sesiuni un mesaj de eroare
function redirect_with_error($message, $extra) {
    $_SESSION['error'] = $message;
    $host  = $_SERVER['HTTP_HOST'];
    header("Location: http://$host/$extra");
    exit;
}

function redirect_with_succes($message, $extra) {
    $_SESSION['succes'] = $message;
    $host  = $_SERVER['HTTP_HOST'];
    header("Location: http://$host/$extra");
    exit;
}

?>