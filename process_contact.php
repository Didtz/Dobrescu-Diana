<?php
// Enable error reporting (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set response header
header('Content-Type: application/json');

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get form data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    // Server-side validation
    $errors = array();
    
    // Validate name (minimum 3 characters)
    if (strlen($name) < 3) {
        $errors['name'] = 'Numele trebuie să aibă cel puțin 3 caractere.';
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Introduceți o adresă de email validă.';
    }
    
    // Validate message (minimum 10 characters)
    if (strlen($message) < 10) {
        $errors['message'] = 'Mesajul trebuie să aibă cel puțin 10 caractere.';
    }
    
    // If there are validation errors
    if (!empty($errors)) {
        echo json_encode(array(
            'success' => false,
            'errors' => $errors,
            'error' => 'Validarea formularului a eșuat.'
        ));
        exit;
    }
    
    // Sanitize data to prevent XSS attacks
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    
    // Optional: Save to database or file
    // You can uncomment and modify the following section to save data
    
    /*
    // Save to file (example)
    $data = array(
        'timestamp' => date('Y-m-d H:i:s'),
        'name' => $name,
        'email' => $email,
        'message' => $message
    );
    $logFile = 'contacts.txt';
    $logEntry = json_encode($data) . "\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Or save to database (example with MySQL)
    // $conn = new mysqli("localhost", "user", "password", "database");
    // if ($conn->connect_error) {
    //     die(json_encode(array('success' => false, 'error' => 'Database connection failed')));
    // }
    // $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
    // $stmt->bind_param("sss", $name, $email, $message);
    // $stmt->execute();
    // $stmt->close();
    // $conn->close();
    */
    
    // Optional: Send email notification
    // $to = 'admin@example.com';
    // $subject = 'Nou contact de la: ' . $name;
    // $body = "Nume: " . $name . "\n";
    // $body .= "Email: " . $email . "\n";
    // $body .= "Mesaj: " . $message . "\n";
    // $headers = "From: " . $email . "\r\n";
    // $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    // mail($to, $subject, $body, $headers);
    
    // Return success response
    echo json_encode(array(
        'success' => true,
        'name' => $name,
        'message' => $message,
        'email' => $email
    ));
    exit;
    
} else {
    // If not POST request
    echo json_encode(array(
        'success' => false,
        'error' => 'Metoda de cerere nu este permisă.'
    ));
    exit;
}
?>
