<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Posts.php';

class AdminUserController
{


    protected $userModel;
    protected $postModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->postModel = new Posts();
    }


    public function showEditUser(array $formData)
    {
        $user_id = intval($formData['user_id'] ?? 0);

        $user = $this->userModel->fetchOne("SELECT name, email, lastname, birthday, profession FROM users WHERE id = ?", [$user_id]);

        if (!$user) {
            $_SESSION['error'] = "User not found.";
            header("Location: /Project1/dashboard");
            exit();
        }

        $name = $user['name'] ?? '';
        $email = $user['email'] ?? '';
        $lastname = $user['lastname'] ?? '';
        $birthday = $user['birthday'] ?? '';
        $profession = $user['profession'] ?? '';

        include __DIR__ . '/../views/admin/editUsers.php';
        exit();
    }

    public function editUser(array $formData)
    {
        $user_id = intval($formData['user_id'] ?? 0);
        $name = trim($formData['name'] ?? '');
        $email = trim($formData['email'] ?? '');
        $lastname = trim($formData['lastname'] ?? '');
        $birthday = trim($formData['birthday'] ?? '');
        $profession = trim($formData['profession'] ?? '');
        $password = $formData['password'] ?? '';

        $user = $this->userModel->getUserById($user_id);
        if (!$user) {
            $_SESSION['error'] = "User not found.";
            header("Location: /Project1/userEdit");
            exit();
        }

        $current_email = $user['email'];

        if ($email !== $current_email) {
            $existingUser = $this->userModel->findByEmail($email);
            if ($existingUser && $existingUser['id'] != $user_id) {
                $_SESSION['error'] = "Email already exists.";
                header("Location: /Project1/userEdit");
                exit();
            }
        }

        $updated = $this->userModel->updateUserProfile($user_id, $name, $email, $lastname, $birthday, $profession);

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $this->userModel->updateUserPassword($user_id, $hashed_password);
        }

        if ($updated) {
            $_SESSION['success'] = "Profile updated successfully.";
        } else {
            $_SESSION['error'] = "Error updating profile.";
        }

        header("Location: /Project1/dashboard");
        exit();
    }


    public function deleteUser()
    {
        $user_id = $_POST['user_id'] ?? null;

        if ($user_id) {
            $this->userModel->delete($user_id);
        }

        header("Location: /Project1/dashboard");
        exit();
    }

    public function showCreate()
    {
        include __DIR__ . '/../views/admin/createNewUser.php';
        exit();
    }

    public function createNewuser(array $formData)
    {
        require_once '/var/www/html/Project1/Validations/Validations.php';

        $validations = new Validations();
        $validations->required('name', $formData['name']);
        $validations->required('lastname', $formData['lastname']);
        $validations->email('email', $formData['email']);
        $validations->required('password', $formData['password']);
        $validations->passwordValidation('password', $formData['password']);
        $validations->required('passwordconfirm', $formData['passwordconfirm']);
        $validations->required('birthday', $formData['birthday']);
        $validations->required('profession', $formData['profession']);

        $role_id = 2;
        $default_picture = '/assets/media/default.jpg';

        if ($formData['password'] !== $formData['passwordconfirm']) {
            $validations->addError('passwordconfirm', "Passwords do not match.");
        }

        if (!$validations->validateBirthday($formData['birthday'])) {
            $validations->addError('birthday', "Invalid birthday date.");
        }

        if ($this->userModel->findByEmail($formData['email'])) {
            $validations->addError('email', "Email already exists.");
        }

        if (!$validations->isValid()) {
            $_SESSION['errors'] = $validations->getErrors();
            $_SESSION['old'] = $formData;
            header("Location: /Project1/create");
            exit();
        }

        $hashedPassword = password_hash($formData['password'], PASSWORD_BCRYPT);

        $created = $this->userModel->createUser($formData['name'], $formData['lastname'], $formData['email'], $hashedPassword, $formData['birthday'], $formData['profession'], $role_id, $default_picture);

        if ($created) {
            $_SESSION['registered'] = true;
            header("Location: /Project1/dashboard");
            exit();
        }

        $_SESSION['error'] = "Error registering user.";
        header("Location: /Project1/create");
        exit();

    }

 



}


?>