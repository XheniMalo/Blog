<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Post</title>
    <link rel="stylesheet" href="/Project1/assets/css/navbar.css">
    <link rel="stylesheet" href="/Project1/assets/css/posts.css">
</head>

<body>
    <?php include("navbar.php"); ?>

    <div class="post-container">
        <div class="post-details">
            <h2>Create New Post</h2>

            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            ?>

            <form method="post" enctype="multipart/form-data" action="/Project1/post" >
            <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_id); ?>">

            <div class="input-field">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="input-field">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="input-field">
                    <label for="image">Upload Image:</label>
                    <input type="file" id="file" name="file[]" accept="image/*" multiple>
                </div>

                <div class="buttons">
                    <button type="submit" name="submitPost" class="btn btn-primary">Post</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
