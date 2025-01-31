<?php

class AdminController
{
    private $conn;
    public function __construct($dbConnection)
    {
        $this->conn = $dbConnection;
    }

    public function showDashboard()
    {
        $users = $this->dashboard();
        include __DIR__ . '/../views/admin/dashboard.php';
        exit();
    }

    public function dashboard()
    {
        $stmt = $this->conn->prepare("SELECT id, email, name, lastname, birthday, profession FROM users WHERE role_id != 1");
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        $stmt->close();

        return $users;
    }

    public function showPosts()
    {

        $user_id = $_POST['user_id'] ?? null;

        $stmt = $this->conn->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $post_id = $row['post_id'];

            $imageStmt = $this->conn->prepare("SELECT path FROM images WHERE post_id = ?");
            $imageStmt->bind_param("i", $post_id);
            $imageStmt->execute();
            $imageResult = $imageStmt->get_result();

            $images = [];
            while ($imageRow = $imageResult->fetch_assoc()) {
                $images[] = $imageRow['path'];
            }

            $row['images'] = $images;
            $posts[] = $row;

            $imageStmt->close();
        }

        $stmt->close();

        include __DIR__ . '/../views/admin/viewPosts.php';
        exit();
    }

    public function showEditUser(array $formData)
    {

        $user_id = intval($formData['user_id'] ?? 0);
        $name = $email = $lastname = $birthday = $profession = '';

        $stmt = $this->conn->prepare("SELECT name, email, lastname, birthday, profession FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($name, $email, $lastname, $birthday, $profession);
        $stmt->fetch();
        $stmt->close();

        if (empty($name)) {
            $_SESSION['error'] = "User not found.";
            header("Location: /Project1/dashboard");
            exit();
        }

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

        if (empty($user_id) || empty($name) || empty($email) || empty($lastname) || empty($birthday) || empty($profession)) {
            $_SESSION['error'] = "All fields except password are required.";
            header("Location: /Project1/userEdit?user_id=" . $user_id);
            exit();
        }

        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $_SESSION['error'] = "Email already exists.";
            header("Location: /Project1/userEdit?user_id=" . $user_id);
            $stmt->close();
            exit();
        }
        $stmt->close();

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, lastname = ?, birthday = ?, profession = ?, password = ? WHERE id = ?");
            $stmt->bind_param("ssssssi", $name, $email, $lastname, $birthday, $profession, $hashed_password, $user_id);
        } else {
            $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, lastname = ?, birthday = ?, profession = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $name, $email, $lastname, $birthday, $profession, $user_id);
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = "Profile updated successfully.";
            header("Location: /Project1/dashboard");
        } else {
            $_SESSION['error'] = "Error updating profile: " . $this->conn->error;
            header("Location: /Project1/userEdit?user_id=" . $user_id);
        }

        $stmt->close();
        exit();
    }

    public function deleteUser()
    {
        $user_id = $_POST['user_id'] ?? null;

        if ($user_id) {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();

            header("Location: /Project1/dashboard");
            exit();
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

        $sql_check = "SELECT id FROM users WHERE email = ?";
        $stmt_check = $this->conn->prepare($sql_check);
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $_SESSION['error'] = "Email already exists.";
            $this->showCreate();
        }

        $password_hashed = password_hash($password, PASSWORD_BCRYPT);

        $sql_insert = "INSERT INTO users (name, lastname, email, password, birthday, profession, role_id, profile_picture) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $this->conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssssssss", $name, $lastname, $email, $password_hashed, $birthday, $profession, $role_id, $default_picture);

        if ($stmt_insert->execute()) {
            $_SESSION['registered'] = true;
            header('Location: /Project1/dashboard');
            exit();
        } else {
            $_SESSION['error'] = "Error registering user: " . $stmt_insert->error;
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

    public function adminProfile()
    {
        $user_id = $_SESSION['user_id'] ?? null;

        if ($user_id === null) {
            header("Location: /Project1/login");
            exit();
        }
        $name = $email = $lastname = $birthday = $profession = $profile_picture = '';

        $stmt = $this->conn->prepare("SELECT name, email, lastname, birthday, profession, profile_picture FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $stmt->bind_result($name, $email, $lastname, $birthday, $profession, $profile_picture);
        $stmt->fetch();
        if ($stmt->fetch()) {
            echo 'Data fetched successfully.';
        }
        $stmt->close();

        $profile_picture_path = '/Project1/assets/media/' . $profile_picture;

        include __DIR__ . '/../views/admin/adminProfile.php';
        exit();
    }

    public function updateProfile()
    {
 
        $user_id = $_SESSION['user_id'] ?? null;

        if ($user_id === null) {
            header("Location: /Project1/login");
            exit();
        }
        if (isset($_POST['update'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $lastname = $_POST['lastname'];
            $birthday = $_POST['birthday'];
            $profession = $_POST['profession'];

            $stmt = $this->conn->prepare("SELECT email FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $current_email = null;
            $stmt->bind_result($current_email);
            $stmt->fetch();
            $stmt->close();

            if ($email !== $current_email) {
                $emailCheck = $this->conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $emailCheck->bind_param("si", $email, $user_id);
                $emailCheck->execute();
                $emailCheck->store_result();
                if ($emailCheck->num_rows > 0) {
                    $_SESSION['error'] = "Email already exists.";
                    session_write_close();
                    header("Location: /Project1/admin");
                    $emailCheck->close();
                    exit();
                }
                $emailCheck->close();
            }

            $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, lastname = ?, birthday = ?, profession = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $name, $email, $lastname, $birthday, $profession, $user_id);

            if ($stmt->execute()) {
                $stmt->close();
                $_SESSION['success'] = "Profile updated successfully.";
                header("Location: /Project1/admin");
                exit();
            } else {
                $stmt->close();
                $_SESSION['error'] = "Error updating profile: " . $this->conn->error;
                header("Location: /Project1/admin");
                exit();
            }
        }
    }

    public function updateProfilePicture()
    {
        $user_id = $_SESSION['user_id'];
        $uploadDir = '/var/www/html/Project1/assets/media/';
        $allowedExt = ['jpg', 'jpeg', 'png'];

        $stmt = $this->conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $currentProfilePicture = '';
        $stmt->bind_result($currentProfilePicture);
        $stmt->fetch();
        $stmt->close();

        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
            $fileExt = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));

            if (!in_array($fileExt, $allowedExt)) {
                $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG are allowed.";
                header("Location: /Project1/admin");
                exit();
            }

            $newFileName = substr(uniqid('', true), -3) . '.' . $fileExt;
            $uploadPath = $uploadDir . $newFileName;
            error_log("Upload Path: " . $uploadPath);


            if ($currentProfilePicture && $currentProfilePicture != 'default.jpg' && file_exists($uploadDir . $currentProfilePicture)) {
                unlink($uploadDir . $currentProfilePicture);
            }

            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadPath)) {
                $stmt = $this->conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
                $stmt->bind_param("si", $newFileName, $user_id);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Profile picture updated successfully.";
                    header("Location: /Project1/admin");
                    exit();
                } else {
                    $_SESSION['error'] = "Failed to update the profile picture in the database.";
                    header("Location: /Project1/admin");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Failed to upload the profile picture. Please check permissions.";
                header("Location: /Project1/admin");
                exit();
            }
        }

        $_SESSION['error'] = "No file was uploaded.";
        header("Location: /Project1/admin");
    }

    public function password()
    {
        include __DIR__ . '/../views/admin/securityadmin.php';
        exit();

    }

    public function changePassword()
    {
        $user_id = $_SESSION['user_id'] ?? null;

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['error'] = 'All fields are required.';
            header("Location: /Project1/passwordadmin");
            exit();
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'New password and confirmation password do not match.';
            header("Location: /Project1/passwordadmin");
            exit();
        }

        $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->store_result();
        $currentHashedPassword = '';
        $stmt->bind_result($currentHashedPassword);
        $stmt->fetch();

        if ($stmt->num_rows > 0 && password_verify($currentPassword, $currentHashedPassword)) {
            $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            $updateStmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $updateStmt->bind_param("si", $hashedNewPassword, $user_id);

            if ($updateStmt->execute()) {
                $_SESSION['success'] = 'Password updated successfully!';
                header("Location: /Project1/admin");
                exit();
            } else {
                $_SESSION['error'] = 'Failed to update the password. Please try again.';
                header("Location: /Project1/password");
                exit();
            }
        } else {
            $_SESSION['error'] = 'Current password is incorrect.';
            header("Location: /Project1/password");
            exit();
        }

    }

}
?>