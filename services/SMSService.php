<?php
class SMSService {
    private $username = 'sandbox';
    private $apiKey = 'atsk_e285a288435fbd5cb6403545c61619db3797486db365faee88a305b064e9322c202498c3';
    private $baseUrl = 'https://api.sandbox.africastalking.com/version1/messaging';

    public function sendSMS($phone, $message) {
        $headers = [
            'apiKey: ' . $this->apiKey,
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json'
        ];

        $data = [
            
            'username' => $this->username,
            'to' => $phone,
            'message' => $message,
          'from'=> 'Health center system'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode == 201;
    }

    public function sendAppointmentConfirmation($phone, $appointment) {
        $message = "Your appointment has been confirmed:\n";
        $message .= "Date: " . $appointment['appointment_date'] . "\n";
        $message .= "Time: " . $appointment['appointment_time'] . "\n";
        $message .= "Service: " . $appointment['service_type'] . "\n";
        $message .= "Thank you for choosing our health center.";

        return $this->sendSMS($phone, $message);
    }

    public function sendAppointmentCancellation($phone, $appointment) {
        $message = "Your appointment has been cancelled:\n";
        $message .= "Date: " . $appointment['appointment_date'] . "\n";
        $message .= "Time: " . $appointment['appointment_time'] . "\n";
        $message .= "Service: " . $appointment['service_type'] . "\n";
        $message .= "Thank you for your understanding.";

        return $this->sendSMS($phone, $message);
    }
}
?> 