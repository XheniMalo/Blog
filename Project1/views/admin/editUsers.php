<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="/Project1/assets/css/editUsers.css">
</head>

<body>
    <?php
    include("navbar.php"); 
    ?>
    <div class="wrapper">
        <div class="card">
            <div class="card-body">
            <?php if (isset($_SESSION['error'])) {
                    echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
                ?>
                <h2>Edit Profile</h2>
                <form method="POST" action="/Project1/userEditing" enctype="multipart/form-data">

                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">


                    <div class="input-field">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required
                            placeholder="">
                    </div>
                    <div class="input-field">
                        <label for="lastname">Last name:</label>
                        <input type="text" id="lastname" name="lastname"
                            value="<?php echo htmlspecialchars($lastname); ?>" required placeholder="">
                    </div>
                    <div class="input-field">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>"
                            required placeholder="">
                    </div>
                    <div class="input-field">
                        <label for="birthday">Birthday:</label>
                        <input type="date" id="birthday" name="birthday"
                            value="<?php echo htmlspecialchars($birthday); ?>" required placeholder="">
                    </div>
                    <div class="input-field">
                        <label for="profession">Profession:</label>
                        <input type="text" id="profession" name="profession"
                            value="<?php echo htmlspecialchars($profession); ?>" required placeholder="">
                    </div>

                    <div class="input-field">
                        <label for="password">New Password:</label>
                        <input type="password" id="password" name="password" placeholder="Enter the new password">
                    </div>

                    <div class="buttons">
                        <button type="submit" name="update" class="btn btn-primary">Update</button>
                    </div>
                    <div class="buttons">
                        <button type="button" class="btn btn-secondary"
                            onclick="window.location.href='dashboard.php'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>