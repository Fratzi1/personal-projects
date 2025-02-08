<?php
session_start();

// Daca a fost validat si are eroare, o vom folosi
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;

// La fel vom utiliza valorile corecte
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];

// Clear session data after displaying
unset($_SESSION['error']);

unset($_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-up</title>
    <link rel="stylesheet" href="style.css?">
</head>
<body>
    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <div>
        Sign-up
    </div>
    <form method="post" action="signup_process.php">
        <label for="first_name">First name</label><br>
        <input type="text" id="first_name" name="first_name" 
               value="<?= htmlspecialchars($form_data['first_name'] ?? '') ?>" required><br>

        <label for="last_name">Last name</label><br>
        <input type="text" id="last_name" name="last_name" 
               value="<?= htmlspecialchars($form_data['last_name'] ?? '') ?>" required><br>

        <label for="email">Email</label><br>
        <input type="text" id="email" name="email" 
               value="<?= htmlspecialchars($form_data['email'] ?? '') ?>" required><br>

        <label for="password">Password:</label><br>
        <input type="password" minlength="8" id="password" name="password" required><br>

        <label for="confirm_password">Confirm Password:</label><br>
        <input type="password" minlength="8" id="confirm_password" name="confirm_password" required><br>
        
        <label for="phone">Phone Number</label><br>
        <select id="country_prefix" name="country_prefix" required>
            <option value="">Country</option>
            <?php
            require_once('db_connect.php');
            $query = 'SELECT * FROM country_phone_codes';
            foreach ($link->query($query) as $row) {
                $selected = ($form_data['country_prefix'] ?? '') == $row['code'] ? 'selected' : '';
                echo "<option value=\"{$row['code']}\" $selected>{$row['country']},{$row['code']}</option>";
            }
            ?>
        </select>    
        <input type="tel" id="phone" name="phone" maxlength="9" style="width: 80px;"
               value="<?= htmlspecialchars($form_data['phone'] ?? '') ?>"><br>

        <label for="user_type">Type of user:</label><br>
        <select id="user_type" name="user_type" required>
            <option value="">Choose</option>
            <option value="client" <?= (isset($form_data['user_type']) && $form_data['user_type'] == 'client') ? 'selected' : '' ?>>Client</option>
            <option value="trainer" <?= (isset($form_data['user_type']) && $form_data['user_type'] == 'trainer') ? 'selected' : '' ?>>Trainer</option>        </select><br>

        <input type="submit" value="Submit">
    </form>
    <p>*All fields are mandatory</p>
</body>
</html>