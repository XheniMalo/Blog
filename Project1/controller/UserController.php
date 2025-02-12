<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Posts.php';
class UserController
{


    protected $userModel;
    protected $postModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->postModel = new Posts();
    }

    public function showHomepage()
    {
        $posts = $this->homepage();
        include __DIR__ . '/../views/user/homepageView.php';
        exit();
    }

    public function homepage()
    {
        $userId = $_SESSION['user_id'];
        $name = "";
        $email = "";

        $user = $this->userModel->getUserById($userId);
        $name = $user['name'] ?? '';
        $email = $user['email'] ?? '';


        $posts = $this->postModel->getAllPosts();
        foreach ($posts as &$post) {
            $post['images'] = $this->postModel->getPostImages($post['post_id']);
        }

        return $posts;
    }
    public function profile()
    {

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SESSION['role'] !== 2) {
            header("Location: /Project1/dashboard");
            exit();
        }

        $userId = $_SESSION['user_id'];

        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            header("Location: /Project1/error");
            exit();
        }

        $name = $user['name'] ?? '';
        $email = $user['email'] ?? '';
        $lastname = $user['lastname'] ?? '';
        $birthday = $user['birthday'] ?? '';
        $profession = $user['profession'] ?? '';
        $profile_picture = $user['profile_picture'] ?? 'default.jpg';

        $profile_picture_path = '/Project1/assets/media/' . $user['profile_picture'];

        include __DIR__ . '/../views/user/profile.php';
        exit();

    }

    public function updateProfile()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'];

        if (isset($_POST['update'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $lastname = $_POST['lastname'];
            $birthday = $_POST['birthday'];
            $profession = $_POST['profession'];

            $user = $this->userModel->getUserById($userId);
            if (!$user) {
                $_SESSION['error'] = "User not found.";
                header("Location: /Project1/profile");
                exit();
            }

            $current_email = $user['email'];

            if ($email !== $current_email) {
                $existingUser = $this->userModel->findByEmail($email);
                if ($existingUser && $existingUser['id'] != $userId) {
                    $_SESSION['error'] = "Email already exists.";
                    header("Location: /Project1/profile");
                    exit();
                }
            }

            $updated = $this->userModel->updateUserProfile($userId, $name, $email, $lastname, $birthday, $profession);

            if ($updated) {
                $_SESSION['success'] = "Profile updated successfully.";
            } else {
                $_SESSION['error'] = "Error updating profile.";
            }

            header("Location: /Project1/profile");
            exit();
        }
    }

    public function updateProfilePicture()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'];
        $uploadDir = '/var/www/html/Project1/assets/media/';
        $allowedExt = ['jpg', 'jpeg', 'png'];

        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            $_SESSION['error'] = "User not found.";
            header("Location: /Project1/profile");
            exit();
        }

        $currentProfilePicture = $user[0]['profile_picture'];

        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
            $fileExt = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));

            if (!in_array($fileExt, $allowedExt)) {
                $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
                header("Location: /Project1/profile");
                exit();
            }

            $newFileName = substr(uniqid('', true), -3) . '.' . $fileExt;
            $uploadPath = $uploadDir . $newFileName;

            if ($currentProfilePicture && $currentProfilePicture !== 'default.jpg' && file_exists($uploadDir . $currentProfilePicture)) {
                unlink($uploadDir . $currentProfilePicture);
            }

            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadPath)) {
                $sql = "UPDATE users SET profile_picture = ? WHERE id = ?";
                $params = [$newFileName, $userId];

                if ($this->userModel->execute($sql, $params)) {
                    $_SESSION['success'] = "Profile picture updated successfully.";
                } else {
                    $_SESSION['error'] = "Failed to update the profile picture in the database.";
                }
            } else {
                $_SESSION['error'] = "Failed to upload the profile picture. Please check permissions.";
            }
        } else {
            $_SESSION['error'] = "No file was uploaded.";
        }

        header("Location: /Project1/profile");
        exit();
    }

    public function password()
    {

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        include __DIR__ . '/../views/user/securityPage.php';
        exit();

    }

    public function changePassword()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'];

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['error'] = 'All fields are required.';
            header("Location: /Project1/password");
            exit();
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'New password and confirmation password do not match.';
            header("Location: /Project1/password");
            exit();
        }

        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            $_SESSION['error'] = "User not found.";
            header("Location: /Project1/password");
            exit();
        }

        $currentHashedPassword = $user['password'];

        if (!password_verify($currentPassword, $currentHashedPassword)) {
            $_SESSION['error'] = 'Current password is incorrect.';
            header("Location: /Project1/password");
            exit();
        }

        $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        $updated = $this->userModel->updateUserPassword($userId, $hashedNewPassword);

        if ($updated) {
            $_SESSION['success'] = 'Password updated successfully!';
            header("Location: " . ($userId == 1 ? "/Project1/adminprofile" : "/Project1/profile"));
        } else {
            $_SESSION['error'] = 'Failed to update the password. Please try again.';
            header("Location: /Project1/password");
        }
        exit();
    }


}

?>