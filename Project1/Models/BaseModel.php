<?php
require_once __DIR__ . '/../Models/Database.php';

class BaseModel extends Database
{

    protected $table;

    public function __construct()
    {
        parent::__construct();
    }


    public function select($sql = "", $params = [])
    {
        if (empty($sql)) {
            $sql = "SELECT * FROM {$this->table}";
        }
        return $this->fetchAll($sql, $params);
    }

    public function fetchOne($sql, $params = [])
    {
        $result = $this->query($sql, $params);
        if ($result === false) {
            return null;
        }
        return $result->fetch_assoc();
    }

    public function fetchAll($sql, $params = [])
    {
        $result = $this->query($sql, $params);
        if ($result === false) {
            return null;
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function execute($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
    
        if ($params) {
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i'; 
                } elseif (is_float($param)) {
                    $types .= 'd'; 
                } else {
                    $types .= 's'; 
                }
            }
    
            $stmt->bind_param($types, ...$params);
        }
        if (!$stmt->execute()) {
            die("Execution Error: " . $stmt->error);
        }
    
        return $stmt->insert_id ?: $stmt->affected_rows;
    }
    

    public function delete($id)
{
    $query = "DELETE FROM {$this->table} WHERE id = ?";
    return $this->execute($query, [$id]); 
}

}


?>