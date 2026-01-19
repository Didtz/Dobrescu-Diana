<?php
/**
 * Login API Endpoint
 * Handles user authentication
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
        
        $email = isset($input['email']) ? trim($input['email']) : '';
        $password = isset($input['password']) ? trim($input['password']) : '';
        
        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email si parola sunt obligatorii']);
            exit();
        }
        
        $database = new Database();
        $conn = $database->getConnection();
        
        if (!$conn) {
            echo json_encode(['success' => false, 'message' => 'Eroare de conexiune la baza de date']);
            exit();
        }
        
        $stmt = $conn->prepare("SELECT id, username, email, password, first_name, last_name, role, is_active FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            $hashedPassword = hash('sha256', $password);
            
            if ($hashedPassword === $user['password']) {
                if ($user['is_active'] == 1) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['logged_in'] = true;
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Autentificare reusita',
                        'user' => [
                            'id' => $user['id'],
                            'username' => $user['username'],
                            'first_name' => $user['first_name'],
                            'last_name' => $user['last_name'],
                            'role' => $user['role']
                        ]
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Contul este inactiv']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Email sau parola incorecta']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Email sau parola incorecta']);
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
