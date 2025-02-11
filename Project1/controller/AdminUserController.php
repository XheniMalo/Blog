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

    public function showPosts()
    {

        $user_id = $_POST['user_id'] ?? null;

        $posts = $this->userModel->fetchAll("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC", [$user_id]);

        foreach ($posts as &$post) {
            $post['images'] = $this->userModel->fetchColumn(
                "SELECT path FROM images WHERE post_id = ?",
                [$post['post_id']]
            );
        }

        include __DIR__ . '/../views/admin/viewPosts.php';
        exit();
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

    public function createNewuser($formData)
    {
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
            $this->showCreate();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email format.";
            $this->showCreate();
        }

        if (!$this->validateBirthday($birthday)) {
            $_SESSION['error'] = "Invalid birthday date. Please ensure it is a valid date and not a future date.";
            $this->showCreate();
        }

        $existingUser = $this->userModel->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);

        if ($existingUser) {
            $_SESSION['error'] = "Email already exists.";
            $this->showCreate();
        }

        $password_hashed = password_hash($password, PASSWORD_BCRYPT);

        $query = "INSERT INTO users (name, lastname, email, password, birthday, profession, role_id, profile_picture) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [$name, $lastname, $email, $password_hashed, $birthday, $profession, $role_id, $default_picture];

        if ($this->userModel->execute($query, $params)) {
            $_SESSION['registered'] = true;
            header('Location: /Project1/dashboard');
            exit();
        } else {
            $_SESSION['error'] = "Error registering user.";
            $this->showCreate();
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


    public function editPost()
    {
        $post_id = $_GET['post_id'] ?? null;
        if (!$post_id) {
            $_SESSION['error'] = "Invalid post ID.";
            header("Location: /Project1/admin");
            exit();
        }

        $post = $this->postModel->fetchOne("SELECT * FROM posts WHERE post_id = ?", [$post_id]);

        if (!$post) {
            $_SESSION['error'] = "Post not found.";
            header("Location: /Project1/admin");
            exit();
        }

        include __DIR__ . '/../views/admin/editPostView.php';
    }

    public function deletePost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Invalid request.";
            header("Location: /Project1/dashboard");
            exit();
        }

        $post_id = $_POST['post_id'] ?? null;

        if (!$post_id) {
            $_SESSION['error'] = "Invalid post ID.";
            header("Location: /Project1/dashboard");
            exit();
        }

        $images = $this->postModel->fetchAll("SELECT path FROM images WHERE post_id = ?", [$post_id]);
        $uploadDir = "/var/www/html/Project1/assets/media/uploads/";

        foreach ($images as $image) {
            $imagePath = $uploadDir . $image['path'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $this->postModel->execute("DELETE FROM images WHERE post_id = ?", [$post_id]);

        $deleted = $this->postModel->execute("DELETE FROM posts WHERE post_id = ?", [$post_id]);

        if ($deleted) {
            $_SESSION['success'] = "Post deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete post.";
        }

        header("Location: /Project1/dashboard");
        exit();
    }




}


?>