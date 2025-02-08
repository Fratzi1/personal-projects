<?php
// Functie care verifica useri

function check_credentials($link, $table, $email, $password) {
    
    // Modify the query to select both user_id and user_type
    $query = "SELECT user_id, user_type, password FROM $table WHERE email = ?";
    $stmt = $link->prepare($query);

    if (!$stmt) {
        die("Query preparation failed: " . $link->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $user_id = null;
    $user_type = '';
    $stored_hashed_password = '';
    $stmt->bind_result($user_id, $user_type, $stored_hashed_password);  // Bind user_type

    // If a user is found, verify the password
    if ($stmt->fetch()) {
        $stmt->close();
        if (password_verify($password, $stored_hashed_password)) {
            
            // Return both user_id and user_type
            return ['user_id' => $user_id, 'user_type' => $user_type];
        } else {
            return false;
        }
    }

    $stmt->close();
    return null;  // No user found
}

// Functie care creaza cookie
function create_first_token ($link, $table, $user_id, $user_type){
    
    $token = bin2hex(random_bytes(16));        

    $created_at = date('Y-m-d H:i:s', time());  // Current time as a string
    $expires_at = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60));
    
    // !!!!!! De schimbat aici false cu true cand o sa am conexiune secure
    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), "/", "", false, true); // 30 days

    // Inseram noua valoare in tabel 
    $query = "INSERT INTO $table (user_id, token, created_at, expires_at, user_type) VALUES (?,?,?,?,?)";
    $stmt = $link->prepare($query);

    if (!$stmt) {
        die("Query preparation failed: " . $link->error);
    }
    $stmt->bind_param("issss", $user_id, $token, $created_at, $expires_at, $user_type);
    $stmt->execute();
    $stmt->close();
}

function check_token($link, $table, $user_id, $user_type) {
    
    $query = "SELECT expires_at, token FROM $table WHERE user_id = ? AND expires_at > NOW()";
    $stmt = $link->prepare($query);

    if (!$stmt) {
        die("Query preparation failed: " . $link->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $expires_at = '';
    $token = '';
    $stmt->bind_result($expires_at, $token);

    // I: avem user care are deja un token si e valid 
    if ($stmt->fetch()) {
        
        $stmt->close();
        setcookie('remember_token', $token, strtotime($expires_at), "/", "", false, true);
    } else {
        
        // II: fie nu are, fie expirat (stergem) => creez unul nou
        $stmt->close();
        
        $check_query = "SELECT COUNT(*) FROM $table WHERE user_id = ?";
        $check_stmt = $link->prepare($check_query);
        if (!$check_stmt) {
            die("Query preparation failed for check: " . $link->error);
        }
        
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        
        $count = 0;
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        // Daca e expirat, sterg
        if ($count > 0) {
            $delete_query = "DELETE FROM $table WHERE user_id = ?";
            $delete_stmt = $link->prepare($delete_query);
            if (!$delete_stmt) {
                die("Query preparation failed for delete: " . $link->error);
            }
            
            $delete_stmt->bind_param("i", $user_id);
            $delete_stmt->execute();
            $delete_stmt->close();
        }

        // Now create a new token for the user
        create_first_token($link, $table, $user_id, $user_type);
    }
}


?>