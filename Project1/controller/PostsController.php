<?php
class PostsController
{
    private $conn;
    public function __construct($dbConnection)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->conn = $dbConnection;
    }

    public function post()
    {

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
            if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
                header("Location: /Project1/login");
                exit();
            }
        include __DIR__ . '/../views/user/addPost.php';
        exit();

    }

    public function addPost()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
            if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
                header("Location: /Project1/login");
                exit();
            }

        $userId = $_SESSION['user_id'] ?? null;

        if ($userId === null) {
            header("Location: /Project1/login");
            exit();
        }

        $title = htmlspecialchars($_POST['title']);
        $description = htmlspecialchars($_POST['description']);
        $images = $_FILES['file'];

        if (empty($images['name'][0])) {
            $_SESSION['error'] = "No images selected.";
            header("Location: /Project1/homepage");
            exit();
        }

        $fileCount = count($images['name']);
        $uploadedPaths = [];
        $uploadSuccess = true;

        for ($i = 0; $i < $fileCount; $i++) {
            if ($images['error'][$i] === UPLOAD_ERR_OK) {
                $targetDir = "/var/www/html/Project1/assets/media/uploads/";
                $imageName = basename($images['name'][$i]);
                $targetFile = $targetDir . $imageName;

                if (move_uploaded_file($images['tmp_name'][$i], $targetFile)) {
                    $uploadedPaths[] = $imageName;
                } else {
                    $uploadSuccess = false;
                    $_SESSION['error'] = "Failed to upload image: " . $images['name'][$i];
                    break;
                }
            } else {
                $uploadSuccess = false;
                $_SESSION['error'] = "Error with file: " . $images['name'][$i];
                break;
            }
        }

        if ($uploadSuccess) {
            $query = "INSERT INTO posts (user_id, title, description) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("iss", $userId, $title, $description);

            if ($stmt->execute()) {
                $postId = $stmt->insert_id;

                foreach ($uploadedPaths as $imageName) {
                    $query = "INSERT INTO images (post_id, path) VALUES (?, ?)";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bind_param("is", $postId, $imageName);

                    if (!$stmt->execute()) {
                        $_SESSION['error'] = "Failed to save image data: " . $stmt->error;
                        header("Location: /Project1/homepage");
                        exit();
                    }
                }

                $_SESSION['success'] = "Post and images uploaded successfully!";
                header("Location: /Project1/homepage");
                exit();
            } else {
                $_SESSION['error'] = "Failed to add post. Error: " . $stmt->error;
                header("Location: /Project1/homepage");
            }
        } else {
            header("Location: /Project1/homepage");
        }

    }

    public function showedit()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
            if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
                header("Location: /Project1/login");
                exit();
            }

        $userId = $_SESSION['user_id'] ?? null;

        $post_id = $_POST['post_id'] ?? null;
        $title = $description = '';

        $stmt = $this->conn->prepare("SELECT title, description FROM posts WHERE post_id = ? ");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $stmt->bind_result($title, $description);
        $stmt->fetch();
        $stmt->close();

        $image_stmt = $this->conn->prepare("SELECT image_id, path FROM images WHERE post_id = ?");
        $image_stmt->bind_param("i", $post_id);
        $image_stmt->execute();
        $image_result = $image_stmt->get_result();
        $images = $image_result->fetch_all(MYSQLI_ASSOC);
        $image_stmt->close();


        include __DIR__ . '/../views/user/editPost.php';
        exit();
    }

    public function editPost()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
            if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
                header("Location: /Project1/login");
                exit();
            }

        $userId = $_SESSION['user_id'] ?? null;

        if ($userId === null) {
            header("Location: /Project1/login");
            exit();
        }

        $postId = $_POST['post_id'] ?? null;

        if ($postId === null) {
            $_SESSION['error'] = "Post ID is missing.";
            header("Location: /Project1/homepage");
            exit();
        }


        $title = htmlspecialchars($_POST['title']);
        $description = htmlspecialchars($_POST['description']);
        $images = $_FILES['file'];

        $query = "UPDATE posts SET title = ?, description = ? WHERE post_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssi", $title, $description, $postId);

        if ($stmt->execute()) {
            if (isset($images) && $images['error'][0] == 0) {
                $fileCount = count($images['name']);
                $uploadedPaths = [];
                $uploadSuccess = true;

                for ($i = 0; $i < $fileCount; $i++) {
                    if ($images['error'][$i] === UPLOAD_ERR_OK) {
                        $targetDir = "/var/www/html/Project1/assets/media/uploads/";
                        $imageName = basename($images['name'][$i]);
                        $targetFile = $targetDir . $imageName;

                        if (move_uploaded_file($images['tmp_name'][$i], $targetFile)) {
                            $uploadedPaths[] = $imageName;
                        } else {
                            $uploadSuccess = false;
                            $_SESSION['error'] = "Failed to upload image: " . $images['name'][$i];
                            break;
                        }
                    } else {
                        $uploadSuccess = false;
                        $_SESSION['error'] = "Error with file: " . $images['name'][$i];
                        break;
                    }
                }

                if ($uploadSuccess) {
                    foreach ($uploadedPaths as $imageName) {
                        $query = "INSERT INTO images (post_id, path) VALUES (?, ?)";
                        $stmt = $this->conn->prepare($query);
                        $stmt->bind_param("is", $postId, $imageName);

                        if (!$stmt->execute()) {
                            $_SESSION['error'] = "Failed to save image data: " . $stmt->error;
                            header("Location: /Project1/homepage");
                            exit();
                        }
                    }
                }
            }

            $_SESSION['success'] = "Post updated successfully!";
            header("Location: /Project1/homepage");
            exit();
        } else {
            $_SESSION['error'] = "Failed to update the post.";
            header("Location: /Project1/homepage");
            exit();
        }

    }

    public function deletePost()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
            if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
                header("Location: /Project1/login");
                exit();
            }

        $userId = $_SESSION['user_id'] ?? null;

        if ($userId === null) {
            header("Location: /Project1/login");
            exit();
        }

        $postId = $_POST['post_id'] ?? null;

        if ($postId === null) {
            $_SESSION['error'] = "Post ID is missing.";
            header("Location: /Project1/homepage");
            exit();
        }

        $stmt = $this->conn->prepare("SELECT user_id FROM posts WHERE post_id = ?");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $_SESSION['error'] = "Post not found.";
            header("Location: /Project1/homepage");
            exit();
        }

        $ownerId = '';
        $stmt->bind_result($ownerId);
        $stmt->fetch();

        if ($ownerId !== $userId) {
            $_SESSION['error'] = "You can only delete your own posts.";
            header("Location: /Project1/homepage");
            exit();
        }
        $stmt->close();

        $stmt = $this->conn->prepare("SELECT path FROM images WHERE post_id = ?");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        $imagePaths = [];

        while ($row = $result->fetch_assoc()) {
            $imagePaths[] = $row['path'];
        }

        $stmt->close();

        foreach ($imagePaths as $imagePath) {
            $filePath = '/var/www/html/Project1/assets/media/uploads/' . $imagePath;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $stmt = $this->conn->prepare("DELETE FROM images WHERE post_id = ?");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $stmt->close();

        $stmt = $this->conn->prepare("DELETE FROM posts WHERE post_id = ?");
        $stmt->bind_param("i", $postId);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Post deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete the post.";
        }
        $stmt->close();

        header("Location: /Project1/homepage");
        exit();
    }

    public function deleteImage()
    {

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
            if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
                header("Location: /Project1/login");
                exit();
            }
        $remove_images = $_POST['remove_images'];

        foreach ($remove_images as $image_id) {
            $stmt = $this->conn->prepare("SELECT path FROM images WHERE image_id = ?");
            $stmt->bind_param("i", $image_id);
            $stmt->execute();
            $image_path = '';
            $stmt->bind_result($image_path);
            $stmt->fetch();
            $stmt->close();

            if (!empty($image_path) && file_exists(__DIR__ . "/Project1/assets/media/uploads/" . $image_path)) {
                unlink(__DIR__ . "/Project1/assets/media/uploads/" . $image_path);
            }

            $delete_stmt = $this->conn->prepare("DELETE FROM images WHERE image_id = ?");
            $delete_stmt->bind_param("i", $image_id);
            $delete_stmt->execute();
            $delete_stmt->close();
        }
        header("Location: /Project1/homepage");
        exit();
    }
}
?>