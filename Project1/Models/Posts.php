<?php
 class Posts extends BaseModel {
    protected $table = 'posts';

    public function __construct() {
        parent::__construct();
    }

    public function getUserPosts($userId)
    {
        return $this->select("SELECT post_id, title, description, created_at FROM posts WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
    }

    public function getPostImages($postId)
    {
        return $this->fetchColumn("SELECT path FROM images WHERE post_id = ?", [$postId]);
    }

    public function getAllPosts()
    {
        return $this->select("
            SELECT posts.post_id, posts.title, posts.description, posts.created_at, users.name 
            FROM posts
            JOIN users ON posts.user_id = users.id
            ORDER BY posts.created_at DESC
        ");
    }

 }

?>