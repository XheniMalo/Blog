<?php
class Database
{
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $conn;

    public function __construct($servername = "localhost", $username = "xhensila-malo", $password = 1234, $dbname = "atis")
    {
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;

        try {
            $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }

    
    }

    public function getConnection()
    {

        return $this->conn;
    }

    public function fetchAll($query, $params = [])
    {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchOne($query, $params = [])
    {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function execute($query, $params = [])
    {
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }
}

?>