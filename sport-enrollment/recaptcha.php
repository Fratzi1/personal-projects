<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content=
        "width=device-width, initial-scale=1.0">

    <!-- Google reCAPTCHA CDN -->
    <script src=
        "https://www.google.com/recaptcha/api.js" async defer>
    </script>
    <link rel="stylesheet" type="text/css" href="style_recaptcha.css?v=1.2" />
</head>

<body>
    <div class="container">
        <!-- HTML Form -->
        
        <form action="recaptcha_process.php" method="post">
            <h3>Contact me form</h3>
            <input type="text" name="name" id="name" 
                placeholder="Enter Name" required>
            <br>

            <input type="text" name="email" id="email" 
                placeholder="Enter Email" required>
            <br>

            <textarea name="message" id="message" 
                placeholder="Enter message" rows="4" cols="50" required></textarea>
            <br>

            <!-- div to show reCAPTCHA -->
            <div class="g-recaptcha" 
                data-sitekey="6Led6JwqAAAAAIxzwom74o1zkP_y713qBM4xK0tc">
            </div>
            <br>

            <button type="submit" name="submit_btn">
                Submit
            </button>
        </form>
    </div>
</body>

</html>