<?php
// session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Posts</title>
    <link rel="stylesheet" href="/Project1/assets/css/navbar.css">
    <link rel="stylesheet" href="/Project1/assets/css/home.css">

</head>

<body>
    <?php include("navbar.php"); ?>

    <div class="posts-container">
    
       

        <?php
        if (empty($posts)) {
            echo '<p>No posts available.</p>';
        } else {
            foreach ($posts as $post) {
                ?>
                <div class="post">
                    <div class="post-content">
                        <div class="post-text">
                        <p><strong>Created by:</strong> <?php echo htmlspecialchars($post['name']); ?></p>
                            <form method="POST" action="/Project1/edit" class="d-inline">
                                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                                <p><?php echo nl2br(htmlspecialchars($post['description'])); ?></p>
                                <p><strong>Posted on:</strong> <?php echo htmlspecialchars($post['created_at']); ?></p>
                                <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['post_id']); ?>">
                                <button type="submit" name="edit" class="btn">Edit</button>
                            </form>

                            <form method="POST" action="/Project1/delete" class="d-inline">
                                <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['post_id']); ?>">
                                <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                        <?php if (!empty($post['images'])): ?>
                            <div class="post-images-container">
                                <?php foreach ($post['images'] as $image): ?>
                                    <div>
                                        <img src="/Project1/assets/media/uploads/<?php echo htmlspecialchars($image); ?>"
                                            alt="Post Image" class="post-image">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</body>

</html>