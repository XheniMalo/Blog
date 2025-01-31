<?php
class UserController
{

    private $conn;

    public function __construct($dbConnection)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->conn = $dbConnection;
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

        $stmt = $this->conn->prepare("SELECT name, email FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($name, $email);
        $stmt->fetch();
        $stmt->close();

        $stmt = $this->conn->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
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

        return $posts;
    }

    public function profile()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            header("Location: /Project1/login");
            exit();
        }
        if ($_SESSION['role'] !== 2) {
            header("Location: /Project1/dashboard");
            exit();
        }
        

    $userId = $_SESSION['user_id'];
    $stmt = $this->conn->prepare("SELECT name, email, lastname, birthday, profession, profile_picture FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $name = $email = $lastname = $birthday = $profession = $profile_picture = '';
    $stmt->bind_result($name, $email, $lastname, $birthday, $profession, $profile_picture);
    $stmt->fetch();
    $stmt->close();

    $profile_picture_path = '/Project1/assets/media/' . $profile_picture;  

    include __DIR__ . '/../views/user/profile.php';
    exit();
}

    public function updateProfile()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
            if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
                header("Location: /Project1/login");
                exit();
            }

        $userId = $_SESSION['user_id'];

        if ($userId === null) {
            header("Location: /login");
            exit();
        }
        if (isset($_POST['update'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $lastname = $_POST['lastname'];
            $birthday = $_POST['birthday'];
            $profession = $_POST['profession'];

            $stmt = $this->conn->prepare("SELECT email FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $current_email = null;
            $stmt->bind_result($current_email);
            $stmt->fetch();
            $stmt->close();

            if ($email !== $current_email) {
                $emailCheck = $this->conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $emailCheck->bind_param("si", $email, $userId);
                $emailCheck->execute();
                $emailCheck->store_result();
                if ($emailCheck->num_rows > 0) {
                    $_SESSION['error'] = "Email already exists.";
                    session_write_close();
                    header("Location: /Project1/profile");
                    $emailCheck->close();
                    exit();
                }
                $emailCheck->close();
            }
            
            $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, lastname = ?, birthday = ?, profession = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $name, $email, $lastname, $birthday, $profession, $userId);

            if ($stmt->execute()) {
                $stmt->close();
                $_SESSION['success'] = "Profile updated successfully.";
                header("Location: /Project1/profile");
                exit();
            } else {
                $stmt->close();
                $_SESSION['error'] = "Error updating profile: " . $this->conn->error;
                header("Location: /Project1/profile");
                exit();
            }
        }
    }

    public function updateProfilePicture()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
            if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
                header("Location: /Project1/login");
                exit();
            }

        $userId = $_SESSION['user_id'];
        $uploadDir = '/var/www/html/Project1/assets/media/'; 
        $allowedExt = ['jpg', 'jpeg', 'png'];

        $stmt = $this->conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $currentProfilePicture = '';
        $stmt->bind_result($currentProfilePicture);
        $stmt->fetch();
        $stmt->close();

        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
            $fileExt = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));

            if (!in_array($fileExt, $allowedExt)) {
                $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG are allowed.";
                header("Location: /Project1/profile");
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
                $stmt->bind_param("si", $newFileName, $userId);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Profile picture updated successfully.";
                    header("Location: /Project1/profile");
                    exit();
                } else {
                    $_SESSION['error'] = "Failed to update the profile picture in the database.";
                    header("Location: /Project1/profile");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Failed to upload the profile picture. Please check permissions.";
                header("Location: /Project1/profile");
                exit();
            }
        }

        $_SESSION['error'] = "No file was uploaded.";
        header("Location: /Project1/profile");
    }

    public function password(){

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
            if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
                header("Location: /Project1/login");
                exit();
            }
            if ($_SESSION['role'] !== 2) {
                header("Location: /Project1/dashboard");
                exit();
            }
        include __DIR__ . '/../views/user/securityPage.php';
        exit();

    }

    public function changePassword(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
            if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
                header("Location: /Project1/login");
                exit();
            }
    
        $userId = $_SESSION['user_id'] ;
    
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
    
        $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->store_result();
        $currentHashedPassword='';
        $stmt->bind_result($currentHashedPassword);
        $stmt->fetch();
    
        if ($stmt->num_rows > 0 && password_verify($currentPassword, $currentHashedPassword)) {
            $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    
            $updateStmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $updateStmt->bind_param("si", $hashedNewPassword, $userId);
    
            if ($updateStmt->execute()) {
                $_SESSION['success'] = 'Password updated successfully!';
                if($userId==1){
                    header("Location: /Project1/adminprofile");
                }
                else {
                    header("Location: /Project1/profile");
                }

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