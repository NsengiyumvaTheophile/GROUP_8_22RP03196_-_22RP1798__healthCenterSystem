Developers: Jean de Dieu Dudengimana       22RP01798
            Theophile Nsengiyumva          22RP03196


Health Appointment Booking System

A USSD-based platform designed to streamline appointment scheduling for rural health center patients. The system enables patients to register, book appointments, receive SMS confirmations, view upcoming appointments, and cancel appointments—all without requiring internet access.

Features
--------

Patient Registration
--------------------

- Capture patient details (name, phone number, ID, village)
- Unique patient ID assignment
- Secure data storage using PDO and MySQL/MariaDB
- Welcome SMS upon successful registration

Appointment Management
-----------------------

- Book appointments with service selection
  - General Consultation
  - Vaccination
  - Maternity
- View upcoming appointments
- Cancel appointments
- SMS notifications for all actions

Navigation
----------

- User-friendly USSD menu system
- "98" to go back one step
- "99" to return to main menu
- Different menus for registered and unregistered users

Technical Requirements
----------------------

        Server Requirements
- PHP 7.0 or higher
- MySQL/MariaDB
- Apache/Nginx web server
- XAMPP (for local development)

 Dependencies
--------------
- PDO PHP Extension
- cURL PHP Extension
- Africa's Talking SMS API

Installation

1. Clone the repository to your web server directory:
```bash
git clone [repository-url]
```

2. Create the database using the provided schema:
```bash
mysql -u root -p < database.sql
```

3. Configure the database connection in `config/Database.php`:
```php
private $host = "localhost";
private $db_name = "health_appointment";
private $username = "your_username";
private $password = "your_password";
```

4. Configure the SMS service in `services/SMSService.php`:
```php
private $username = 'sandbox';
private $apiKey = 'your_api_key';
```

5. Set up your USSD gateway to point to the `ussd.php` endpoint

Directory Structure
-------------------

├── config/
│   └── Database.php
├── models/
│   ├── Patient.php
│   └── Appointment.php
├── services/
│   └── SMSService.php
├── database.sql
├── ussd.php
└── README.md

 Usage

 USSD Menu Flow

 Unregistered Users
-------------------

1. Dial USSD code
2. Select "1" to register
3. Enter required information:
   - Full name
   - National ID
   - Village
4. Receive welcome SMS
5. Access main menu

Registered Users
----------------

1. Dial USSD code
2. Access main menu with options:
   - Book Appointment
   - View Appointments
   - Cancel Appointment
   - Exit

Navigation
----------

- Use "98" to go back one step
- Use "99" to return to main menu
- Follow on-screen instructions for other options

SMS Notifications
-----------------

The system sends SMS notifications for:
- Registration confirmation
- Appointment booking confirmation
- Appointment cancellation confirmation

All SMS messages are sent from "Health center system" sender ID.

Security Features
-----------------

- PDO prepared statements for SQL injection prevention
- Input sanitization
- Secure API key storage
- Unique patient identification

Support

For technical support or questions, please contact the system administrator via Email: djados088@gmail.com or Tel: +250784842622
and my collabrator email:nsengiyumvatheophile08@gmail.com   telno: 0780888084

Thank you!!!!!.

