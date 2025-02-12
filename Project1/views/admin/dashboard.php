<?php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/Project1/assets/css/adminDashboard.css">

</head>

<body>
    <?php include("navbar.php"); ?>
    <div class="wrapper">
        <h1 class="mt-5">Admin Dashboard</h1>
        <?php
        if (isset($_SESSION['error'])):
            ?>
            <div class="error-message"
                style="color: red; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; margin-bottom: 20px;">
                <?= $_SESSION['error']; ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (!empty($users)): ?>
            <div class="cards-container">
                <?php foreach ($users as $user): ?>
                    <div class="card mt-3">
                        <div class="card-body">
                            <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                            <p class="card-text"><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                            <p class="card-text"><strong>Last Name:</strong> <?php echo htmlspecialchars($user['lastname']); ?>
                            </p>
                            <p class="card-text"><strong>Birthday:</strong> <?php echo htmlspecialchars($user['birthday']); ?>
                            </p>
                            <p class="card-text"><strong>Profession:</strong>
                                <?php echo htmlspecialchars($user['profession']); ?></p>

                            <div class="buttons">
                                <form method="POST" action="/Project1/posts" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="btn btn-primary">View Posts</button>
                                </form>
                                <form method="POST" action="/Project1/userEdit" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="btn btn-primary">Edit</button>
                                </form>


                                <form method="POST" action="/Project1/deleteUser" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                                </form>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>
    </div>
</body>

</html>