<?php

class Database {
    private $host = "localhost";
    private $db_name = "pharmacy_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            $this->conn->set_charset("utf8mb4");
            
            if ($this->conn->connect_error) {
                throw new Exception("Conexiune esuata: " . $this->conn->connect_error);
            }
        } catch(Exception $e) {
            echo "Eroare conexiune: " . $e->getMessage();
        }
        return $this->conn;
    }
}


session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config/database.php';
    
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
} else {
    echo json_encode(['success' => false, 'message' => 'Metoda invalida']);
}


session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config/database.php';
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $firstName = isset($input['name']) ? trim($input['name']) : '';
    $lastName = isset($input['surname']) ? trim($input['surname']) : '';
    $email = isset($input['email']) ? trim($input['email']) : '';
    $password = isset($input['password']) ? trim($input['password']) : '';
    $phone = isset($input['phone']) ? trim($input['phone']) : '';
    $age = isset($input['age']) ? intval($input['age']) : 0;
    
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
    
    $username = explode('@', $email)[0] . '_' . rand(1000, 9999);
    
    $hashedPassword = hash('sha256', $password);
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, first_name, last_name, phone, role) VALUES (?, ?, ?, ?, ?, ?, 'student')");
    $stmt->bind_param("ssssss", $username, $email, $hashedPassword, $firstName, $lastName, $phone);
    
    if ($stmt->execute()) {
        $userId = $conn->insert_id;
        
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['first_name'] = $firstName;
        $_SESSION['last_name'] = $lastName;
        $_SESSION['role'] = 'student';
        $_SESSION['logged_in'] = true;
        
        echo json_encode([
            'success' => true,
            'message' => 'Cont creat cu succes',
            'user' => [
                'id' => $userId,
                'username' => $username,
                'first_name' => $firstName,
                'last_name' => $lastName
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Eroare la crearea contului: ' . $conn->error]);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Metoda invalida']);
}


session_start();
session_destroy();
header('Location: ../index.html');
exit();


session_start();
header('Content-Type: application/json');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo json_encode([
        'logged_in' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'first_name' => $_SESSION['first_name'],
            'last_name' => $_SESSION['last_name'],
            'role' => $_SESSION['role']
        ]
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}


session_start();
header('Content-Type: application/json');

require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Eroare de conexiune']);
    exit();
}

$query = "SELECT m.*, GROUP_CONCAT(c.name) as categories 
          FROM medicines m 
          LEFT JOIN medicine_categories mc ON m.id = mc.medicine_id 
          LEFT JOIN categories c ON mc.category_id = c.id 
          GROUP BY m.id 
          ORDER BY m.name";

$result = $conn->query($query);

$medicines = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $medicines[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'price' => floatval($row['price']),
            'stock' => intval($row['stock_quantity']),
            'prescription' => $row['requires_prescription'] == 1,
            'categories' => $row['categories'] ? explode(',', $row['categories']) : [],
            'sideEffects' => $row['side_effects'] ? explode(',', $row['side_effects']) : []
        ];
    }
}

echo json_encode(['success' => true, 'medicines' => $medicines]);
$conn->close();


session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Trebuie sa fiti autentificat']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config/database.php';
    
    $input = json_decode(file_get_contents('php://input'), true);
    $cart = isset($input['cart']) ? $input['cart'] : [];
    
    if (empty($cart)) {
        echo json_encode(['success' => false, 'message' => 'Cosul este gol']);
        exit();
    }
    
    $database = new Database();
    $conn = $database->getConnection();
    
    $total = 0;
    foreach ($cart as $item) {
        $total += floatval($item['price']);
    }
    
    $conn->begin_transaction();
    
    try {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
        $userId = $_SESSION['user_id'];
        $stmt->bind_param("id", $userId, $total);
        $stmt->execute();
        $orderId = $conn->insert_id;
        $stmt->close();
        
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, medicine_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($cart as $item) {
            $medicineStmt = $conn->prepare("SELECT id FROM medicines WHERE name = ?");
            $medicineStmt->bind_param("s", $item['name']);
            $medicineStmt->execute();
            $medicineResult = $medicineStmt->get_result();
            
            if ($medicineResult->num_rows > 0) {
                $medicine = $medicineResult->fetch_assoc();
                $medicineId = $medicine['id'];
                $quantity = 1;
                $unitPrice = floatval($item['price']);
                $subtotal = $unitPrice * $quantity;
                
                $stmt->bind_param("iiidd", $orderId, $medicineId, $quantity, $unitPrice, $subtotal);
                $stmt->execute();
            }
            $medicineStmt->close();
        }
        
        $stmt->close();
        $conn->commit();
        
        echo json_encode(['success' => true, 'message' => 'Comanda plasata cu succes', 'order_id' => $orderId]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Eroare la plasarea comenzii: ' . $e->getMessage()]);
    }
    
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Metoda invalida']);
}
?>