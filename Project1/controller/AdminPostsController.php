<?php
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Posts.php';

class AdminPostsController{

    protected $userModel;
    protected $postModel;

    public function __construct(){
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