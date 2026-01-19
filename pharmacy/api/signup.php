<?php
/**
 * Signup API Endpoint
 * Handles user registration
 */

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $firstName = isset($input['name']) ? trim($input['name']) : '';
        $lastName = isset($input['surname']) ? trim($input['surname']) : '';
        $email = isset($input['email']) ? trim($input['email']) : '';
        $password = isset($input['password']) ? trim($input['password']) : '';
        $phone = isset($input['phone']) ? trim($input['phone']) : '';
        $age = isset($input['age']) ? intval($input['age']) : 0;
        $address = isset($input['address']) ? trim($input['address']) : '';
        $cnp = isset($input['cnp']) ? trim($input['cnp']) : '';
        
        if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Toate campurile obligatorii trebuie completate']);
            exit();
        }
        
        if ($age < 18) {
            echo json_encode(['success' => false, 'message' => 'Trebuie sa aveti cel putin 18 ani']);
            exit();
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Email invalid']);
            exit();
        }
        
        $database = new Database();
        $conn = $database->getConnection();
        
        if (!$conn) {
            echo json_encode(['success' => false, 'message' => 'Eroare de conexiune la baza de date']);
            exit();
        }
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Email-ul este deja inregistrat']);
            $stmt->close();
            $conn->close();
            exit();
        }
        $stmt->close();
        
        // Generate unique username
        $baseUsername = strtolower(str_replace(' ', '.', $firstName . '.' . $lastName));
        $username = $baseUsername;
        $counter = 1;
        
        while (true) {
            $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $checkStmt->bind_param("s", $username);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $checkStmt->close();
            
            if ($checkResult->num_rows === 0) {
                break;
            }
            
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        // Hash password
        $hashedPassword = hash('sha256', $password);
        
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, first_name, last_name, phone, address, age, cnp, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'customer')");
        $stmt->bind_param("sssssssss", $username, $email, $hashedPassword, $firstName, $lastName, $phone, $address, $age, $cnp);
        
        if ($stmt->execute()) {
            $userId = $conn->insert_id;
            
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['first_name'] = $firstName;
            $_SESSION['last_name'] = $lastName;
            $_SESSION['role'] = 'customer';
            $_SESSION['logged_in'] = true;
            
            echo json_encode([
                'success' => true,
                'message' => 'Cont creat cu succes',
                'user' => [
                    'id' => $userId,
                    'username' => $username,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'role' => 'customer'
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Eroare la crearea contului: ' . $conn->error]);
        }
        
        $stmt->close();
        $conn->close();
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Eroare: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metoda invalida']);
}
?>
