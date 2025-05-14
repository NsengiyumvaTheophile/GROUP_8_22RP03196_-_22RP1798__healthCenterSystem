<?php
class Appointment {
    private $conn;
    private $table_name = "appointments";

    public $id;
    public $patient_id;
    public $service_type;
    public $appointment_date;
    public $appointment_time;
    public $status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (patient_id, service_type, appointment_date, appointment_time, status, created_at)
                VALUES
                (:patient_id, :service_type, :appointment_date, :appointment_time, :status, :created_at)";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->service_type = htmlspecialchars(strip_tags($this->service_type));
        $this->appointment_date = htmlspecialchars(strip_tags($this->appointment_date));
        $this->appointment_time = htmlspecialchars(strip_tags($this->appointment_time));
        $this->status = "scheduled";
        $this->created_at = date('Y-m-d H:i:s');

        // Bind values
        $stmt->bindParam(":patient_id", $this->patient_id);
        $stmt->bindParam(":service_type", $this->service_type);
        $stmt->bindParam(":appointment_date", $this->appointment_date);
        $stmt->bindParam(":appointment_time", $this->appointment_time);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":created_at", $this->created_at);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getPatientAppointments($patient_id) {
        $query = "SELECT a.*, p.name as patient_name, p.phone_number 
                FROM " . $this->table_name . " a
                JOIN patients p ON a.patient_id = p.id
                WHERE a.patient_id = :patient_id
                ORDER BY a.appointment_date ASC, a.appointment_time ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->execute();
        return $stmt;
    }

    public function cancel() {
        $query = "UPDATE " . $this->table_name . "
                SET status = 'cancelled'
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function checkAvailability($date, $time) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . "
                WHERE appointment_date = :date 
                AND appointment_time = :time 
                AND status = 'scheduled'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":time", $time);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Assuming max 3 appointments per time slot
        return $row['count'] < 3;
    }
}
?> 