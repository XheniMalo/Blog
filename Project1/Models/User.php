<?php
require_once __DIR__ . '/../Models/BaseModel.php';

class User extends BaseModel{
    protected $table = 'users';

    public function __construct(){
        parent::__construct();
    }

    public function getUserById($id)
    {
        return $this->select("SELECT * FROM users WHERE id = ?", [$id]);
    }

}

?>