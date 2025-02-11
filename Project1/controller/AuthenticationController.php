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
        $name = $formData['name'] ?? '';
        $lastname = $formData['lastname'] ?? '';
        $email = $formData['email'] ?? '';
        $password = $formData['password'] ?? '';
        $passwordConfirm = $formData['passwordconfirm'] ?? '';
        $birthday = $formData['birthday'] ?? '';
        $profession = $formData['profession'] ?? '';
        $role_id = 2;
        $default_picture = '/assets/media/default.jpg';

        if ($password !== $passwordConfirm) {
            $_SESSION['error'] = "Passwords do not match.";
            header("Location: /Project1/register");
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email format.";
            header("Location: /Project1/register");
            exit();
        }

        if (!$this->validateBirthday($birthday)) {
            $_SESSION['error'] = "Invalid birthday date.";
            header("Location: /Project1/register");
            exit();
        }

        if ($this->userModel->findByEmail($email)) {
            $_SESSION['error'] = "Email already exists.";
            header("Location: /Project1/register");
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $created = $this->userModel->createUser($name, $lastname, $email, $hashedPassword, $birthday, $profession, $role_id, $default_picture);

        if ($created) {
            $_SESSION['registered'] = true;
            header("Location: /Project1/login");
            exit();
        }

        $_SESSION['error'] = "Error registering user.";
        header("Location: /Project1/register");
        exit();
    }

    private function validateBirthday(string $birthday): bool
    {
        $date = DateTime::createFromFormat('Y-m-d', $birthday);
        if (!$date)
            return false;

        $today = new DateTime();
        return $date <= $today && $today->diff($date)->y <= 120;
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
