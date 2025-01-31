<?php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create a new user</title>
    <link rel="stylesheet" href="/Project1/assets/css/createuser.css">
</head>

<body>
    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    include("navbar.php");
    ?>
    <div class="wrapper">
        <form method="post" action="/Project1/createUser">
            <h2>Create a new user</h2>
            <div class="input-field">
                <input type="text" name="name" id="name" required>
                <label for="name">Enter the name</label>
            </div>
            <div class="input-field">
                <input type="text" name="lastname" id="lastname" required>
                <label for="lastname">Enter the last name</label>
            </div>
            <div class="input-field">
                <input type="email" name="email" id="email" required>
                <label for="email">Enter the email</label>
            </div>
            <div class="input-field">
                <input type="password" name="password" id="password" required>
                <label for="password">Enter the password</label>
            </div>
            <div class="input-field">
                <input type="password" name="passwordconfirm" id="password" required>
                <label for="passwordconfirm">Confirm your password</label>
            </div>
            <div class="input-field">
                <input type="date" name="birthday" id="birthday" required>
                <label for="birthday">Enter the birthday</label>
            </div>
            <div class="input-field">
                <input type="text" name="profession" id="profession" required>
                <label for="profession">Enter the profession</label>
            </div>
            <button type="submit">Create</button>

            <div class="buttons">
                <button type="button" class="btn btn-secondary"
                    onclick="window.location.href='../user/profile.php'">Cancel</button>
            </div>
        </form>
    </div>
</body>

</html>