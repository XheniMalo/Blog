
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        <form method="post" action="/Project1/login">
            <h2>Log in</h2>
            <div class="input-field">
                <input type="email" name="email" id="email" required>
                <label for="email">Enter your email</label>
            </div>
            <div class="input-field">
                <input type="password" name="password" id="password" required>
                <label for="password">Enter your password</label>
            </div>
            <button type="submit">Log In</button>
            <div class="register">
                <p>Don't have an account? <a href="/Project1/register">Register</a></p>
            </div>
        </form>
    </div>
</body>
</html>

