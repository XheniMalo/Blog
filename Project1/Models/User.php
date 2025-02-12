<?php
require_once __DIR__ . '/../Models/BaseModel.php';


class User extends BaseModel
{
    protected $table = 'users';

    public function __construct()
    {
        parent::__construct();
    }

    public function getUserById($id)
    {
        return $this->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->conn->prepare("SELECT id, name, lastname, email, password, role_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ?: null;
    }

    public function createUser(string $name, string $lastname, string $email, string $password, string $birthday, string $profession, int $role_id, string $profile_picture): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO users (name, lastname, email, password, birthday, profession, role_id, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssis", $name, $lastname, $email, $password, $birthday, $profession, $role_id, $profile_picture);
        return $stmt->execute();
    }


    public function updateUserProfile($userId, $name, $email, $lastname, $birthday, $profession)   
    {
        $sql = "UPDATE users SET name = ?, email = ?, lastname = ?, birthday = ?, profession = ? WHERE id = ?";
        $params = [$name, $email, $lastname, $birthday, $profession, $userId];

        return $this->execute($sql, $params);
    }

    public function updateUserPassword($userId, $hashedPassword)
    {
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        return $this->execute($sql, [$hashedPassword, $userId]);
    }

}

?>