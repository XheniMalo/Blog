<?php

class AuthenticationController
{
    private $conn;

    public function __construct($dbConnection)
    {
        $this->conn = $dbConnection;
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

        $stmt = $this->conn->prepare("SELECT id, password, role_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $user_id = null;
        $hashed_password = '';
        $role_id = null;
        $stmt->bind_result($user_id, $hashed_password, $role_id);

        if ($stmt->fetch()) {
            if (password_verify($password, $hashed_password)) {
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = $role_id;

                if ($role_id == 1) {
                    header("Location: /Project1/dashboard");
                } else {
                    header("Location: /Project1/homepage");
                }
                exit();
            } else {
                $_SESSION['error'] = "Invalid email or password.";
            }
        } else {
            $_SESSION['error'] = "Email not found.";
        }
    }

    public function showRegistrationForm()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        include __DIR__ . '/../views/guest/registration-page.php';
        exit();
    }

    public function register($formData)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $name = $formData['name'];
        $lastname = $formData['lastname'];
        $email = $formData['email'];
        $password = $formData['password'];
        $passwordconfirm = $formData['passwordconfirm'];
        $birthday = $formData['birthday'];
        $profession = $formData['profession'];
        $role_id = 2;
        $default_picture = '/assets/media/default.jpg';

        if ($password !== $passwordconfirm) {
            $_SESSION['error'] = "Passwords do not match.";
            $this->showRegistrationForm();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email format.";
            $this->showRegistrationForm();
        }

        if (!$this->validateBirthday($birthday)) {
            $_SESSION['error'] = "Invalid birthday date. Please ensure it is a valid date and not a future date.";
            $this->showRegistrationForm();
        }

        $sql_check = "SELECT id FROM users WHERE email = ?";
        $stmt_check = $this->conn->prepare($sql_check);
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $_SESSION['error'] = "Email already exists.";
            $this->showRegistrationForm();
        }

        $password_hashed = password_hash($password, PASSWORD_BCRYPT);

        $sql_insert = "INSERT INTO users (name, lastname, email, password, birthday, profession, role_id, profile_picture) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $this->conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssssssss", $name, $lastname, $email, $password_hashed, $birthday, $profession, $role_id, $default_picture);

        if ($stmt_insert->execute()) {
            $_SESSION['registered'] = true;
            header('Location: /Project1/login');
            exit();
        } else {
            $_SESSION['error'] = "Error registering user: " . $stmt_insert->error;
            $this->showRegistrationForm();
        }
    }

    private function validateBirthday($birthday)
    {
        $date = DateTime::createFromFormat('Y-m-d', $birthday);
        if ($date === false) {
            return false;
        }

        $today = new DateTime();
        if ($date > $today) {
            return false;
        }

        $age = $today->diff($date)->y;
        return $age <= 120;
    }

    public function logout(): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        session_unset();
    
        session_destroy();
    
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        var_dump($_SESSION);
    
        header('Location: /Project1/login');
        exit();
    }
}
?>