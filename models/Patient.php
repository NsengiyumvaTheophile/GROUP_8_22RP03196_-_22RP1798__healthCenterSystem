<?php
class Patient {
    private $conn;
    private $table_name = "patients";

    public $id;
    public $name;
    public $phone_number;
    public $national_id;
    public $village;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (name, phone_number, national_id, village, created_at)
                VALUES
                (:name, :phone_number, :national_id, :village, :created_at)";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->phone_number = htmlspecialchars(strip_tags($this->phone_number));
        $this->national_id = htmlspecialchars(strip_tags($this->national_id));
        $this->village = htmlspecialchars(strip_tags($this->village));
        $this->created_at = date('Y-m-d H:i:s');

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":phone_number", $this->phone_number);
        $stmt->bindParam(":national_id", $this->national_id);
        $stmt->bindParam(":village", $this->village);
        $stmt->bindParam(":created_at", $this->created_at);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getByPhone($phone) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE phone_number = :phone LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":phone", $phone);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt;
    }

    public function exists($phone) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE phone_number = :phone";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":phone", $phone);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }
}
?> 