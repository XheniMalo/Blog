<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="/Project1/assets/css/guest.css">
</head>

<body>
    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    ?>
    <div class="wrapper">
        <form method="post" action="/Project1/register">

            <h2>Register</h2>
            <div class="input-field">
                <input type="text" name="name" id="name" required>
                <label for="name">Enter your name</label>
            </div>
            <div class="input-field">
                <input type="text" name="lastname" id="lastname" required>
                <label for="lastname">Enter your last name</label>
            </div>
            <div class="input-field">
                <input type="email" name="email" id="email" required>
                <label for="email">Enter your email</label>
            </div>
            <div class="input-field">
                <input type="password" name="password" id="password" required>
                <label for="password">Enter your password</label>
            </div>
            <div class="input-field">
                <input type="password" name="passwordconfirm" id="password" required>
                <label for="passwordconfirm">Confirm your password</label>
            </div>
            <div class="input-field">
                <input type="date" name="birthday" id="birthday" required>
                <label for="birthday">Enter your birthday</label>
            </div>
            <div class="input-field">
                <input type="text" name="profession" id="profession" required>
                <label for="profession">Enter your profession</label>
            </div>
            <button type="submit">Register</button>
            <div class="register">
                <p>Already have an account? <a href="/Project1/login">Log in</a></p>
            </div>
        </form>
    </div>
</body>

</html>