<?php

class UserController
{
    private $database;

    public function __construct()
    {
        $this->database = new Database();
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

        $user = $this->database->fetchOne("SELECT name, email FROM users WHERE id = ?", [$userId]);

        $posts = $this->database->fetchAll("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC", [$userId]);

        foreach ($posts as &$post) {
            $post['images'] = $this->database->fetchAll("SELECT path FROM images WHERE post_id = ?", [$post['post_id']]);
        }

        return $posts;
    }

    public function profile()
    {

        $userId = $_SESSION['user_id'];
        $user = $this->database->fetchOne("SELECT name, email, lastname, birthday, profession, profile_picture FROM users WHERE id = ?", [$userId]);

        $profile_picture_path = '/Project1/assets/media/' . ($user['profile_picture'] ?? 'default.jpg');

        include __DIR__ . '/../views/user/profile.php';
        exit();
    }

    public function updateProfile()
    {
        $userId = $_SESSION['user_id'];

        if (isset($_POST['update'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $lastname = $_POST['lastname'];
            $birthday = $_POST['birthday'];
            $profession = $_POST['profession'];

            $currentEmail = $this->database->fetchOne("SELECT email FROM users WHERE id = ?", [$userId])['email'] ?? null;

            if ($email !== $currentEmail) {
                $existingUser = $this->database->fetchOne("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $userId]);
                if ($existingUser) {
                    $_SESSION['error'] = "Email already exists.";
                    header("Location: /Project1/profile");
                    exit();
                }
            }

            $this->database->execute(
                "UPDATE users SET name = ?, email = ?, lastname = ?, birthday = ?, profession = ? WHERE id = ?",
                [$name, $email, $lastname, $birthday, $profession, $userId]
            );

            $_SESSION['success'] = "Profile updated successfully.";
            header("Location: /Project1/profile");
            exit();
        }
    }

    public function updateProfilePicture()
    {
        $userId = $_SESSION['user_id'];
        $uploadDir = '/var/www/html/Project1/assets/media/';
        $allowedExt = ['jpg', 'jpeg', 'png'];

        $currentProfilePicture = $this->database->fetchOne("SELECT profile_picture FROM users WHERE id = ?", [$userId])['profile_picture'] ?? '';

        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
            $fileExt = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));

            if (!in_array($fileExt, $allowedExt)) {
                $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG are allowed.";
                header("Location: /Project1/profile");
                exit();
            }

            $newFileName = substr(uniqid('', true), -3) . '.' . $fileExt;
            $uploadPath = $uploadDir . $newFileName;

            if ($currentProfilePicture && $currentProfilePicture !== 'default.jpg' && file_exists($uploadDir . $currentProfilePicture)) {
                unlink($uploadDir . $currentProfilePicture);
            }

            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadPath)) {
                $this->database->execute("UPDATE users SET profile_picture = ? WHERE id = ?", [$newFileName, $userId]);

                $_SESSION['success'] = "Profile picture updated successfully.";
                header("Location: /Project1/profile");
                exit();
            } else {
                $_SESSION['error'] = "Failed to upload the profile picture. Please check permissions.";
                header("Location: /Project1/profile");
                exit();
            }
        }

        $_SESSION['error'] = "No file was uploaded.";
        header("Location: /Project1/profile");
    }

    public function password()
    {

        include __DIR__ . '/../views/user/securityPage.php';
        exit();
    }

    public function changePassword()
    {
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

        $user = $this->database->fetchOne("SELECT password FROM users WHERE id = ?", [$userId]);

        if ($user && password_verify($currentPassword, $user['password'])) {
            $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $this->database->execute("UPDATE users SET password = ? WHERE id = ?", [$hashedNewPassword, $userId]);

            $_SESSION['success'] = 'Password updated successfully!';
            header("Location: " . ($userId == 1 ? "/Project1/adminprofile" : "/Project1/profile"));
            exit();
        } else {
            $_SESSION['error'] = 'Current password is incorrect.';
            header("Location: /Project1/password");
            exit();
        }
    }
}
