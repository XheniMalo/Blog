<?php


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <link rel="stylesheet" href="/Project1/assets/css/navbar.css">
    <link rel="stylesheet" href="/Project1/assets/css/profile.css">
</head>

<body>
    <?php include("navbar.php"); ?>

    <div class="profile-container">
        <div class="profile-details">
            <h2>Your Profile</h2>
            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            ?>

            <form method="post" action="/Project1/profile">

                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <div class="input-field">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
                <div class="input-field">
                    <label for="lastname">Last name:</label>
                    <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($lastname); ?>"
                        required>
                </div>
                <div class="input-field">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>"
                        required>
                </div>
                <div class="input-field">
                    <label for="birthday">Birthday:</label>
                    <input type="date" id="birthday" name="birthday" value="<?php echo htmlspecialchars($birthday); ?>"
                        required>
                </div>
                <div class="input-field">
                    <label for="profession">Profession:</label>
                    <input type="text" id="profession" name="profession"
                        value="<?php echo htmlspecialchars($profession); ?>" required>
                </div>

                <div class="buttons">
                    <button type="button" name="security" class="btn btn-primary"
                        onclick="window.location.href='/Project1/password'">Security</button>
                </div>

                <div class="buttons">
                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>

        <form method="post"  action="/Project1/profilepic" enctype="multipart/form-data">
            <div class="profile-picture">
                <img src="<?php echo htmlspecialchars($profile_picture_path); ?>" alt="Profile Picture">
            </div>
            <div class="input-field">
                        <label for="profile_picture">Profile Picture:</label>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
            </div>
            <div class="buttons">
            <button type="submit" name="update" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</body>
</html>