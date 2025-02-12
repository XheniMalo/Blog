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
    <title>Create a new user</title>
    <link rel="stylesheet" href="/Project1/assets/css/createuser.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>

<body>
    <?php
    include("navbar.php");
    ?>
    <div class="wrapper">
        <form method="post" action="/Project1/createUser">
            <h2>Create a new user</h2>

            <div class="input-field">
                <input type="text" name="name" id="name"
                    class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>"
                    value="<?php echo htmlspecialchars($old['name'] ?? ''); ?>">
                    <label for="name" class="form-label">Enter the user's name</label>
                <div class="invalid-feedback"><?php echo $errors['name'] ?? ''; ?></div>
            </div>


            <div class="input-field">
                <input type="text" name="lastname" id="lastname"
                    class="form-control <?php echo isset($errors['lastname']) ? 'is-invalid' : ''; ?>"
                    value="<?php echo htmlspecialchars($old['lastname'] ?? ''); ?>">
                    <label for="lastname" class="form-label">Enter the user's last name</label>
                <div class="invalid-feedback"><?php echo $errors['lastname'] ?? ''; ?></div>
            </div>

            <div class="input-field">
                <input type="email" name="email" id="email"
                    class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>"
                    value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
                    <label for="email" class="form-label">Enter the user's email</label>
                <div class="invalid-feedback"><?php echo $errors['email'] ?? ''; ?></div>
            </div>

            <div class="input-field">
                <input type="password" name="password" id="password"
                    class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>">
                    <label for="password" class="form-label">Enter the user's password</label>
                <div class="invalid-feedback"><?php echo $errors['password'] ?? ''; ?></div>
            </div>

            <div class="input-field">
                <input type="password" name="passwordconfirm" id="passwordconfirm"
                    class="form-control <?php echo isset($errors['passwordconfirm']) ? 'is-invalid' : ''; ?>">
                    <label for="passwordconfirm" class="form-label">Confirm the user's password</label>
                <div class="invalid-feedback"><?php echo $errors['passwordconfirm'] ?? ''; ?></div>
            </div>

            <div class="input-field">
                <input type="date" name="birthday" id="birthday"
                    class="form-control <?php echo isset($errors['birthday']) ? 'is-invalid' : ''; ?>"
                    value="<?php echo htmlspecialchars($old['birthday'] ?? ''); ?>">
                    <label for="birthday" class="form-label">Enter the user's birthday</label>
                <div class="invalid-feedback"><?php echo $errors['birthday'] ?? ''; ?></div>
            </div>

            <div class="input-field">
                <input type="text" name="profession" id="profession"
                    class="form-control <?php echo isset($errors['profession']) ? 'is-invalid' : ''; ?>"
                    value="<?php echo htmlspecialchars($old['profession'] ?? ''); ?>">
                    <label for="profession" class="form-label">Enter the user's profession</label>
                <div class="invalid-feedback"><?php echo $errors['profession'] ?? ''; ?></div>
            </div>
            
            <button type="submit">Create</button>

            <div class="buttons">
                <button type="button" class="btn btn-secondary"
                    onclick="window.location.href='/Project1/dashboard'">Cancel</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>

</html>