-- Create database
CREATE DATABASE IF NOT EXISTS health_appointment;
USE health_appointment;

-- Create patients table
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(15) NOT NULL UNIQUE,
    national_id VARCHAR(20) NOT NULL UNIQUE,
    village VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_phone (phone_number),
    INDEX idx_national_id (national_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create appointments table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    service_type VARCHAR(50) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('scheduled', 'cancelled', 'completed') NOT NULL DEFAULT 'scheduled',
    created_at DATETIME NOT NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    INDEX idx_patient_date (patient_id, appointment_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 