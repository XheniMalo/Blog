<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="/Project1/assets/css/posts.css">

</head>

<body>

    <div class="wrapper">
        <?php include("navbar.php") ?>
        <h2>Edit Post</h2>
        <form method="post" action="/Project1/editPost" enctype="multipart/form-data">
            <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_id); ?>">
            <div class="input-field">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>

            <div class="input-field">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <div class="input-field">
                <label for="file">Upload Image:</label>
                <input type="file" id="file" name="file[]" accept="image/*" multiple>
            </div>
            <div class="buttons">
                <button type="submit" name="edit" class="btn btn-primary">Update Post</button>
            </div>
        </form>

        <?php if (count($images) > 0): ?>
            <?php foreach ($images as $image): ?>
                <form method="post" action="/Project1/deleteImage">
                    <div>
                        <img src="/Project1/assets/media/uploads/<?php echo htmlspecialchars($image['path']); ?>"
                            alt="Post Image" width="100">
                        <button type="submit" name="remove_images[]"
                            value="<?php echo htmlspecialchars($image['image_id']); ?>">Delete</button>
                    </div>
                </form>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No images found for this post.</p>
        <?php endif; ?>

    </div>
</body>

</html>