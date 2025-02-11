<?php

class Database
{
    protected $conn;

    public function __construct()
    {
        $servername = "localhost";
        $username = "xhensila-malo";
        $password = "1234";
        $dbname = "atis";

        $this->conn = new mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function closeConnection()
    {
        if ($this->conn) {
            $this->conn->close();
        }

        return $this->conn;
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);

        if ($params) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    public function fetchColumn(string $query, array $params = []): array
    {
        $stmt = $this->conn->prepare($query);
        if ($params) {
            $types = str_repeat("s", count($params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $columns = [];
        while ($row = $result->fetch_assoc()) {
            $columns[] = array_values($row)[0];
        }

        $stmt->close();
        return $columns;
    }

}

?>