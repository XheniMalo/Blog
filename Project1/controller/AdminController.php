<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Posts.php';

class AdminController
{
    protected $userModel;
    protected $postModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->postModel = new Posts();
    }

    public function showDashboard()
    {
        $users = $this->dashboard();
        include __DIR__ . '/../views/admin/dashboard.php';
        exit();
    }

    public function dashboard()
    {
        return $this->userModel->fetchAll("SELECT id, email, name, lastname, birthday, profession FROM users WHERE role_id != 1");
    }

    public function adminProfile()
    {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            header("Location: /Project1/login");
            exit();
        }

        $user = $this->userModel->fetchOne("SELECT name, email, lastname, birthday, profession, profile_picture FROM users WHERE id = ?", [$user_id]);

        if (!$user) {
            $_SESSION['error'] = "Admin not found.";
            header("Location: /Project1/dashboard");
            exit();
        }

        $name = $user['name'] ?? '';
        $email = $user['email'] ?? '';
        $lastname = $user['lastname'] ?? '';
        $birthday = $user['birthday'] ?? '';
        $profession = $user['profession'] ?? '';
        $profile_picture = $user['profile_picture'] ?? 'default.jpg';

        $profile_picture_path = '/Project1/assets/media/' . ($user['profile_picture'] ?? 'default.jpg');

        include __DIR__ . '/../views/admin/adminProfile.php';
        exit();
    }

    public function updateProfile(array $formData)
    {
        $user_id = $_SESSION['user_id'] ?? null;
      
        $name = trim($formData['name'] ?? '');
        $email = trim($formData['email'] ?? '');
        $lastname = trim($formData['lastname'] ?? '');
        $birthday = trim($formData['birthday'] ?? '');
        $profession = trim($formData['profession'] ?? '');


        $existingUser = $this->userModel->fetchOne("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $user_id]);
        if ($existingUser) {
            $_SESSION['error'] = "Email already exists.";
            header("Location: /Project1/admin");
            exit();
        }

        $updated = $this->userModel->updateUserProfile( $user_id,$name, $email, $lastname, $birthday, $profession);

        if ($updated) {
            $_SESSION['success'] = "Profile updated successfully.";
        } else {
            $_SESSION['error'] = "Error updating profile.";
        }
        header("Location: /Project1/admin");
        exit();
    }

    public function updateProfilePicture()
    {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            header("Location: /Project1/login");
            exit();
        }

        if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== 0) {
            $_SESSION['error'] = "No file was uploaded.";
            header("Location: /Project1/admin");
            exit();
        }

        $allowedExt = ['jpg', 'jpeg', 'png'];
        $fileExt = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExt, $allowedExt)) {
            $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG are allowed.";
            header("Location: /Project1/admin");
            exit();
        }

        $newFileName = uniqid() . '.' . $fileExt;
        $uploadDir = __DIR__ . '/../assets/media/';
        $uploadPath = $uploadDir . $newFileName;

        if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadPath)) {
            $_SESSION['error'] = "Failed to upload the profile picture.";
            header("Location: /Project1/admin");
            exit();
        }

        $this->userModel->execute("UPDATE users SET profile_picture = ? WHERE id = ?", [$newFileName, $user_id]);
        $_SESSION['success'] = "Profile picture updated successfully.";
        header("Location: /Project1/admin");
        exit();
    }

    public function password()
    {
        include __DIR__ . '/../views/admin/securityadmin.php';
        exit();
    }

    public function changePassword(array $formData)
    {
        $user_id = $_SESSION['user_id'] ?? null;
      
        $currentPassword = $formData['current_password'] ?? '';
        $newPassword = $formData['new_password'] ?? '';
        $confirmPassword = $formData['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['error'] = "All fields are required.";
            header("Location: /Project1/passwordadmin");
            exit();
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = "New password and confirmation password do not match.";
            header("Location: /Project1/passwordadmin");
            exit();
        }

        $user = $this->userModel->fetchOne("SELECT password FROM users WHERE id = ?", [$user_id]);
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            $_SESSION['error'] = "Current password is incorrect.";
            header("Location: /Project1/passwordadmin");
            exit();
        }

        $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $updated = $this->userModel->updateUserPassword($user_id, $hashedNewPassword);

        $_SESSION['success'] = "Password updated successfully!";
        header("Location: /Project1/admin");
        exit();
    }
}
