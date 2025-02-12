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
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="/Project1/assets/css/guest.css">


</head>

<body>

    <div class="wrapper">
        <form method="post" action="/Project1/register">

            <h2>Register</h2>

            <div class="input-field">
                <input type="text" name="name" id="name" 
                    class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>"
                    value="<?php echo htmlspecialchars($old['name'] ?? ''); ?>">
                <label for="name">Enter your name</label>
                <div class="invalid-feedback"><?php echo $errors['name'] ?? ''; ?></div>
            </div>

            <div class="input-field">
                <input type="text" name="lastname" id="lastname"
                    class="form-control <?php echo isset($errors['lastname']) ? 'is-invalid' : ''; ?>"
                    value="<?php echo htmlspecialchars($old['lastname'] ?? ''); ?>">
                <label for="lastname">Enter your last name</label>
                <div class="invalid-feedback"><?php echo $errors['lastname'] ?? ''; ?></div>
            </div>

            <div class="input-field">
                <input type="email" name="email" id="email"
                    class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>"
                    value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
                <label for="email">Enter your email</label>
                <div class="invalid-feedback"><?php echo $errors['email'] ?? ''; ?></div>
            </div>

            <div class="input-field">
                <input type="password" name="password" id="password"
                    class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>">
                <label for="password">Enter your password</label>
                <div class="invalid-feedback"><?php echo $errors['password'] ?? ''; ?></div>
            </div>

            <div class="input-field">
                <input type="password" name="passwordconfirm" id="passwordconfirm"
                    class="form-control <?php echo isset($errors['passwordconfirm']) ? 'is-invalid' : ''; ?>">
                <label for="passwordconfirm">Confirm your password</label>
                <div class="invalid-feedback"><?php echo $errors['passwordconfirm'] ?? ''; ?></div>
            </div>

            <div class="input-field">
                <input type="date" name="birthday" id="birthday"
                    class="form-control <?php echo isset($errors['birthday']) ? 'is-invalid' : ''; ?>"
                    value="<?php echo htmlspecialchars($old['birthday'] ?? ''); ?>">
                <label for="birthday">Enter your birthday</label>
                <div class="invalid-feedback"><?php echo $errors['birthday'] ?? ''; ?></div>
            </div>

            <div class="input-field">
                <input type="text" name="profession" id="profession"
                    class="form-control <?php echo isset($errors['profession']) ? 'is-invalid' : ''; ?>"
                    value="<?php echo htmlspecialchars($old['profession'] ?? ''); ?>">
                <label for="profession">Enter your profession</label>
                <div class="invalid-feedback"><?php echo $errors['profession'] ?? ''; ?></div>
            </div>

            <button type="submit">Register</button>

            <div class="register">
                <p>Already have an account? <a href="/Project1/login">Log in</a></p>
            </div>

        </form>
    </div>
</body>

</html>