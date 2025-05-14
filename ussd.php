<?php
require_once 'config/Database.php';
require_once 'models/Patient.php';
require_once 'models/Appointment.php';
require_once 'services/SMSService.php';

// Get USSD parameters
$sessionId = $_POST['sessionId'];
$serviceCode = $_POST['serviceCode'];
$phoneNumber = $_POST['phoneNumber'];
$text = $_POST['text'];

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize objects
$patient = new Patient($db);
$appointment = new Appointment($db);
$smsService = new SMSService();

// Split text into array
$textArray = explode('*', $text);
$userLevel = count($textArray);

// Check if user is registered
$isRegistered = $patient->exists($phoneNumber);

// Function to get main menu with welcome message
function getMainMenu($patientData) {
    $response = "CON Welcome " . $patientData['name'] . " to Health Center System\n";
    $response .= "1. Book Appointment\n";
    $response .= "2. View Appointments\n";
    $response .= "3. Cancel Appointment\n";
    $response .= "4. Exit";
    return $response;
}

// Main menu logic
if ($text == "") {
    if (!$isRegistered) {
        $response = "CON Welcome to Health Center System\n";
        $response .= "1. Register\n";
        $response .= "2. Exit";
    } else {
        $patientData = $patient->getByPhone($phoneNumber)->fetch(PDO::FETCH_ASSOC);
        $response = getMainMenu($patientData);
    }
} else {
    // Handle unregistered user flow
    if (!$isRegistered) {
        switch ($userLevel) {
            case 1:
                if ($textArray[0] == "1") {
                    $response = "CON Please enter your full name:\n";
                    $response .= "98. Go Back\n";
                    $response .= "99. Main Menu";
                } else {
                    $response = "END Thank you for using our service.";
                }
                break;
            case 2:
                if ($textArray[1] == "98") {
                    $response = "CON Welcome to Health Center System\n";
                    $response .= "1. Register\n";
                    $response .= "2. Exit";
                } else if ($textArray[1] == "99") {
                    $response = "CON Welcome to Health Center System\n";
                    $response .= "1. Register\n";
                    $response .= "2. Exit";
                } else {
                    $response = "CON Please enter your National ID:\n";
                    $response .= "98. Go Back\n";
                    $response .= "99. Main Menu";
                }
                break;
            case 3:
                if ($textArray[2] == "98") {
                    $response = "CON Please enter your full name:\n";
                    $response .= "98. Go Back\n";
                    $response .= "99. Main Menu";
                } else if ($textArray[2] == "99") {
                    $response = "CON Welcome to Health Center System\n";
                    $response .= "1. Register\n";
                    $response .= "2. Exit";
                } else {
                    $response = "CON Please enter your village:\n";
                    $response .= "98. Go Back\n";
                    $response .= "99. Main Menu";
                }
                break;
            case 4:
                if ($textArray[3] == "98") {
                    $response = "CON Please enter your National ID:\n";
                    $response .= "98. Go Back\n";
                    $response .= "99. Main Menu";
                } else if ($textArray[3] == "99") {
                    $response = "CON Welcome to Health Center System\n";
                    $response .= "1. Register\n";
                    $response .= "2. Exit";
                } else {
                    // Save registration
                    $patient->name = $textArray[1];
                    $patient->national_id = $textArray[2];
                    $patient->village = $textArray[3];
                    $patient->phone_number = $phoneNumber;
                    
                    if ($patient->create()) {
                        // Send welcome SMS
                        $welcomeMessage = "Welcome " . $patient->name . " to Health Center System\n";
                        $welcomeMessage .= "Your registration was successful.\n";
                         $welcomeMessage .= "Thank you for choosing our health center.";
                        
                        $smsService->sendSMS($phoneNumber, $welcomeMessage);
                        
                        $response = "END Registration successful!";
                    } else {
                        $response = "END Registration failed. Please try again later.";
                    }
                }
                break;
        }
    } else {
        // Handle registered user flow
        $patientData = $patient->getByPhone($phoneNumber)->fetch(PDO::FETCH_ASSOC);
        
        switch ($textArray[0]) {
            case "1": // Book Appointment
                switch ($userLevel) {
                    case 1:
                        $response = "CON Select service:\n";
                        $response .= "1. General Consultation\n";
                        $response .= "2. Vaccination\n";
                        $response .= "3. Maternity\n";
                        $response .= "98. Go Back\n";
                        $response .= "99. Main Menu";
                        break;
                    case 2:
                        if ($textArray[1] == "98") {
                            $response = getMainMenu($patientData);
                        } else if ($textArray[1] == "99") {
                            $response = getMainMenu($patientData);
                        } else {
                            $response = "CON Enter appointment date (YYYY-MM-DD):\n";
                            $response .= "98. Go Back\n";
                            $response .= "99. Main Menu";
                        }
                        break;
                    case 3:
                        if ($textArray[2] == "98") {
                            $response = "CON Select service:\n";
                            $response .= "1. General Consultation\n";
                            $response .= "2. Vaccination\n";
                            $response .= "3. Maternity\n";
                            $response .= "98. Go Back\n";
                            $response .= "99. Main Menu";
                        } else if ($textArray[2] == "99") {
                            $response = getMainMenu($patientData);
                        } else {
                            $response = "CON Select time slot:\n";
                            $response .= "1. 09:00 AM\n";
                            $response .= "2. 10:00 AM\n";
                            $response .= "3. 11:00 AM\n";
                            $response .= "4. 02:00 PM\n";
                            $response .= "5. 03:00 PM\n";
                            $response .= "98. Go Back\n";
                            $response .= "99. Main Menu";
                        }
                        break;
                    case 4:
                        if ($textArray[3] == "98") {
                            $response = "CON Enter appointment date (YYYY-MM-DD):\n";
                            $response .= "98. Go Back\n";
                            $response .= "99. Main Menu";
                        } else if ($textArray[3] == "99") {
                            $response = getMainMenu($patientData);
                        } else {
                            // Save appointment
                            $serviceTypes = [
                                "1" => "General Consultation",
                                "2" => "Vaccination",
                                "3" => "Maternity"
                            ];
                            $timeSlots = [
                                "1" => "09:00",
                                "2" => "10:00",
                                "3" => "11:00",
                                "4" => "14:00",
                                "5" => "15:00"
                            ];
                            
                            $appointment->patient_id = $patientData['id'];
                            $appointment->service_type = $serviceTypes[$textArray[1]];
                            $appointment->appointment_date = $textArray[2];
                            $appointment->appointment_time = $timeSlots[$textArray[3]];
                            
                            if ($appointment->checkAvailability($appointment->appointment_date, $appointment->appointment_time)) {
                                if ($appointment->create()) {
                                    $appointmentData = [
                                        'appointment_date' => $appointment->appointment_date,
                                        'appointment_time' => $appointment->appointment_time,
                                        'service_type' => $appointment->service_type
                                    ];
                                    $smsService->sendAppointmentConfirmation($phoneNumber, $appointmentData);
                                    $response = "END Appointment booked successfully! You will receive an SMS confirmation.";
                                } else {
                                    $response = "END Failed to book appointment. Please try again.";
                                }
                            } else {
                                $response = "END This time slot is full. Please try another time.";
                            }
                        }
                        break;
                }
                break;
            case "2": // View Appointments
                if ($textArray[0] == "2" && $userLevel > 1) {
                    if ($textArray[1] == "98" || $textArray[1] == "99") {
                        $response = getMainMenu($patientData);
                    } else {
                        $response = "END Invalid option selected.";
                    }
                } else {
                    $stmt = $appointment->getPatientAppointments($patientData['id']);
                    
                    if ($stmt->rowCount() > 0) {
                        $response = "CON Your appointments:\n";
                        $counter = 1;
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            if ($row['status'] == 'scheduled') {
                                $response .= $counter . ". " . $row['service_type'] . " - " . 
                                           $row['appointment_date'] . " " . $row['appointment_time'] . "\n";
                                $counter++;
                            }
                        }
                        $response .= "98. Go Back\n";
                        $response .= "99. Main Menu";
                    } else {
                        $response = "END You have no upcoming appointments.";
                    }
                }
                break;
            case "3": // Cancel Appointment
                if ($textArray[0] == "3" && $userLevel > 1) {
                    if ($textArray[1] == "98" || $textArray[1] == "99") {
                        $response = getMainMenu($patientData);
                    } else {
                        $stmt = $appointment->getPatientAppointments($patientData['id']);
                        $appointments = [];
                        $counter = 1;
                        
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            if ($row['status'] == 'scheduled') {
                                $appointments[$counter] = $row;
                                $counter++;
                            }
                        }
                        
                        if (isset($appointments[$textArray[1]])) {
                            $selectedAppointment = $appointments[$textArray[1]];
                            $appointment->id = $selectedAppointment['id'];
                            if ($appointment->cancel()) {
                                $smsService->sendAppointmentCancellation($phoneNumber, $selectedAppointment);
                                $response = "END Appointment cancelled successfully.";
                            } else {
                                $response = "END Failed to cancel appointment. Please try again.";
                            }
                        } else {
                            $response = "END Invalid appointment selection.";
                        }
                    }
                } else {
                    $stmt = $appointment->getPatientAppointments($patientData['id']);
                    
                    if ($stmt->rowCount() > 0) {
                        $response = "CON Select appointment to cancel:\n";
                        $counter = 1;
                        $appointments = [];
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            if ($row['status'] == 'scheduled') {
                                $response .= $counter . ". " . $row['service_type'] . " - " . 
                                           $row['appointment_date'] . " " . $row['appointment_time'] . "\n";
                                $appointments[$counter] = $row;
                                $counter++;
                            }
                        }
                        $response .= "98. Go Back\n";
                        $response .= "99. Main Menu";
                    } else {
                        $response = "END You have no appointments to cancel.";
                    }
                }
                break;
            case "4":
                $response = "END Thank you for using our service.";
                break;
            case "98":
                $response = getMainMenu($patientData);
                break;
            case "99":
                $response = getMainMenu($patientData);
                break;
            default:
                $response = "END Invalid option selected.";
                break;
        }
    }
}

// Print the response
header('Content-type: text/plain');
echo $response;
?> 