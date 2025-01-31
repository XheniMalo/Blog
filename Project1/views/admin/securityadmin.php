<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security</title>
    <link rel="stylesheet" href="/Project1/assets/css/createuser.css">
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="card-body">
                <?php
                if (isset($_SESSION['error'])) {
                echo '<div class="error-message">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
                }
                include('navbar.php')
                ?>
                <h2>Change Your Password</h2>
                <form method="post" action="/Project1/passwordadmin">
                    <div class="input-field">
                        <input type="password" id="current_password" name="current_password"
                            placeholder="Enter your current password" required>
                    </div>
                    <div class="input-field">
                        <input type="password" id="new_password" name="new_password"
                            placeholder="Enter your new password">
                    </div>

                    <div class="input-field">
                        <input type="password" id="confirm_password" name="confirm_password"
                            placeholder="Confirm your new password">
                    </div>

                    <div class="buttons">
                        <button type="submit" name="update" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary"
                            onclick="window.location.href='/Project1/admin'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>