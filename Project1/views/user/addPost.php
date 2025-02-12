<?php
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Post</title>
    <link rel="stylesheet" href="/Project1/assets/css/navbar.css">
    <link rel="stylesheet" href="/Project1/assets/css/posts.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>

<body>
    <?php include("navbar.php"); ?>

    <div class="post-container">
        <div class="post-details">
            <h2>Add a new post</h2>

            <form method="post" enctype="multipart/form-data" action="/Project1/post">
                <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_id); ?>">

                <div class="input-field">
                    <label for="title" class="form-label">Enter the title</label>
                    <input type="text" name="title" id="title"
                        class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>"
                        value="<?php echo htmlspecialchars($old['title'] ?? ''); ?>">
                    <div class="invalid-feedback"><?php echo $errors['title'] ?? ''; ?></div>
                </div>

                <div class="input-field">
                    <label for="description" class="form-label">Enter the description</label>
                    <input type="text" name="description" id="description"
                        class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>"
                        value="<?php echo htmlspecialchars($old['description'] ?? ''); ?>">
                    <div class="invalid-feedback"><?php echo $errors['description'] ?? ''; ?></div>
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