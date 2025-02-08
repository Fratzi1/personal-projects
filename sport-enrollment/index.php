<?php

    require_once 'session_cookie_check.php';
    $link->close();
    $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;

    $form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
    // Clear session data after displaying
    unset($_SESSION['error']);

    unset($_SESSION['form_data']);
    
    $pageTitle = "Index";
    // Contine titlu + navbar
    include 'header.php';

    if ($logged_user_id == 'client'){
        include 'cooper_test.php';
    }
?>    
    

    <!--  
    Cookie consent pop-up - nefunctional inca
    <div id="cookie-consent">
        <p>This website uses essential cookies to ensure the proper functioning of the website, including user login. By continuing to use this site, you consent to our cookie policy.</p>
        <button id="accept-cookies">I Accept</button>
        <button id="decline-cookies">Decline</button>
    </div>
    <script src="cookie_consent.js"></script>
        -->

    <?php
        require_once 'recaptcha.php';
    ?>
    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
</body>
</html>
