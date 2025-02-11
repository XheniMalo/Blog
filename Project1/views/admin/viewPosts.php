
<?php 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/Project1/assets/css/home.css">
</head>

<body>
    <?php include("navbar.php"); ?>
    <div class="posts-container">
        <?php if (empty($posts)) {
            echo '<p>No posts available.</p>';
        } else {
            foreach ($posts as $post) {
                ?>
                <div class="post">
                    <div class="post-content">
                        <div class="post-text">
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($post['description'])); ?></p>
                            <p><strong>Posted on:</strong> <?php echo htmlspecialchars($post['created_at']); ?></p>

                            <form action="/Project1/adminedits" method="post">
                                <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['post_id']); ?>">
                                <button type="submit" class="btn">Edit</button>
                            </form>

                            <form action="/Project1/admindelete" method="post" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['post_id']); ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>

                            <?php if (!empty($post['images'])): ?>
                            <div class="post-images-container">
                                <?php foreach ($post['images'] as $image): ?>
                                    <img src="/Project1/assets/media/uploads/<?php echo htmlspecialchars($image); ?>" 
                                         alt="Post Image" class="post-image">
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