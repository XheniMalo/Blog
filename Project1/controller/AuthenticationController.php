<?php
require_once __DIR__ . '/../Models/User.php';

class AuthenticationController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showLogin(): void
    {
        include __DIR__ . '/../views/guest/login.php';
        exit();
    }

    public function login(array $formData): void
    {
        $email = $formData['email'] ?? null;
        $password = $formData['password'] ?? null;

        if (!$email || !$password) {
            $_SESSION['error'] = "Email and password are required.";
            header("Location: /Project1/login");
            exit();
        }

        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role_id'];

            header("Location: " . ($user['role_id'] == 1 ? "/Project1/dashboard" : "/Project1/homepage"));
            exit();
        }

        $_SESSION['error'] = "Invalid email or password.";
        header("Location: /Project1/login");
        exit();
    }

    public function showRegistrationForm(): void
    {
        include __DIR__ . '/../views/guest/registration-page.php';
        exit();
    }

    public function register(array $formData): void
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
            header("Location: /Project1/register");
            exit();
        }

        $hashedPassword = password_hash($formData['password'], PASSWORD_BCRYPT);

        $created = $this->userModel->createUser($formData['name'], $formData['lastname'], $formData['email'], $hashedPassword, $formData['birthday'], $formData['profession'], $role_id, $default_picture);

        if ($created) {
            $_SESSION['registered'] = true;
            header("Location: /Project1/login");
            exit();
        }

        $_SESSION['error'] = "Error registering user.";
        header("Location: /Project1/login");
        exit();
    }

    public function logout(): void
    {
        session_start();
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 42000, '/');

        header('Location: /Project1/login');
        exit();
    }
}
