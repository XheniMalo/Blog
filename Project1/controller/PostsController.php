<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Posts.php';
class PostsController
{

    protected $userModel;
    protected $postModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->postModel = new Posts();
    }

    public function post()
    {

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        include __DIR__ . '/../views/user/addPost.php';
        exit();

    }

    public function addPost()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
     
        $title = htmlspecialchars($_POST['title'] ?? '');
        $description = htmlspecialchars($_POST['description'] ?? '');
        $images = $_FILES['file'] ?? null;

        if (empty($title) || empty($description)) {
            $_SESSION['error'] = "Title and description are required.";
            header("Location: /Project1/homepage");
            exit();
        }

        if (!$images || empty($images['name'][0])) {
            $_SESSION['error'] = "No images selected.";
            header("Location: /Project1/homepage");
            exit();
        }

        $uploadDir = "/var/www/html/Project1/assets/media/uploads/";
        $allowedExt = ['jpg', 'jpeg', 'png'];

        $postId = $this->postModel->execute("INSERT INTO posts (user_id, title, description) VALUES (?, ?, ?)", [$userId, $title, $description]);

        if (!$postId) {
            $_SESSION['error'] = "Failed to add post.";
            header("Location: /Project1/homepage");
            exit();
        }

        foreach ($images['name'] as $key => $imageName) {
            if ($images['error'][$key] !== UPLOAD_ERR_OK) {
                $_SESSION['error'] = "Error uploading file: " . $imageName;
                header("Location: /Project1/homepage");
                exit();
            }

            $fileExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
            if (!in_array($fileExt, $allowedExt)) {
                $_SESSION['error'] = "Invalid file type: " . $imageName;
                header("Location: /Project1/homepage");
                exit();
            }

            $newFileName = uniqid() . '.' . $fileExt;
            $targetFile = $uploadDir . $newFileName;

            if (move_uploaded_file($images['tmp_name'][$key], $targetFile)) {
                $this->postModel->execute("INSERT INTO images (post_id, path) VALUES (?, ?)", [$postId, $newFileName]);
            } else {
                $_SESSION['error'] = "Failed to upload image: " . $imageName;
                header("Location: /Project1/homepage");
                exit();
            }
        }

        $_SESSION['success'] = "Post and images uploaded successfully!";
        header("Location: /Project1/homepage");
        exit();
    }


    public function showedit()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        $post_id = $_POST['post_id'] ?? null;

        $post = $this->postModel->fetchOne("SELECT title, description FROM posts WHERE post_id = ?", [$post_id]);

        if (!$post) {
            $_SESSION['error'] = "Post not found.";
            header("Location: /Project1/homepage");
            exit();
        }

        $title = $post['title'];
        $description = $post['description'];

        $images = $this->postModel->fetchAll("SELECT image_id, path FROM images WHERE post_id = ?", [$post_id]);

        include __DIR__ . '/../views/user/editPost.php';
        exit();
    }


    public function editPost()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        $postId = $_POST['post_id'] ?? null;
    

        $title = htmlspecialchars($_POST['title']);
        $description = htmlspecialchars($_POST['description']);
        $images = $_FILES['file'] ?? null;


        $updateSuccess = $this->postModel->execute(
            "UPDATE posts SET title = ?, description = ? WHERE post_id = ?",
            [$title, $description, $postId]
        );

        if ($updateSuccess === false) {
            $_SESSION['error'] = "Failed to update the post.";
            header("Location: /Project1/homepage");
            exit();
        }

        if ($images && $images['error'][0] === UPLOAD_ERR_OK) {
            $targetDir = "/var/www/html/Project1/assets/media/uploads/";
            $fileCount = count($images['name']);
            $uploadedPaths = [];
            $uploadSuccess = true;

            for ($i = 0; $i < $fileCount; $i++) {
                if ($images['error'][$i] === UPLOAD_ERR_OK) {
                    $imageName = uniqid('img_') . '_' . basename($images['name'][$i]);
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
                    $this->postModel->execute("INSERT INTO images (post_id, path) VALUES (?, ?)", [$postId, $imageName]);
                }
            }
        }

        $_SESSION['success'] = "Post updated successfully!";
        header("Location: /Project1/homepage");
        exit();
    }

    public function deletePost()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        $postId = $_POST['post_id'] ?? null;

        $ownerId = $this->userModel->fetchColumn("SELECT user_id FROM posts WHERE post_id = ?", [$postId]);

        if (!$ownerId) {
            $_SESSION['error'] = "Post not found.";
            header("Location: /Project1/homepage");
            exit();
        }

        $imagePaths = $this->userModel->fetchAll("SELECT path FROM images WHERE post_id = ?", [$postId]);

        $uploadDir = "/var/www/html/Project1/assets/media/uploads/";
        foreach ($imagePaths as $image) {
            $filePath = $uploadDir . $image['path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $this->userModel->execute("DELETE FROM images WHERE post_id = ?", [$postId]);

        $deleteSuccess = $this->userModel->execute("DELETE FROM posts WHERE post_id = ?", [$postId]);

        if ($deleteSuccess) {
            $_SESSION['success'] = "Post deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete the post.";
        }

        header("Location: /Project1/homepage");
        exit();
    }


    public function deleteImage()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $removeImages = $_POST['remove_images'] ?? [];

        if (empty($removeImages)) {
            $_SESSION['error'] = "No images selected for deletion.";
            header("Location: /Project1/homepage");
            exit();
        }
        $uploadDir = "/var/www/html/Project1/assets/media/uploads/";

        foreach ($removeImages as $imageId) {
            $imagePath = $this->userModel->fetchColumn("SELECT path FROM images WHERE image_id = ?", [$imageId]);

            if ($imagePath) {
                $filePath = $uploadDir . $imagePath;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                $this->userModel->execute("DELETE FROM images WHERE image_id = ?", [$imageId]);
            }
        }

        $_SESSION['success'] = "Images deleted successfully!";
        header("Location: /Project1/homepage");
        exit();
    }

}
?>