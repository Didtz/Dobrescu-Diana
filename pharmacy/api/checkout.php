<?php
/**
 * Checkout API Endpoint
 * Processes user orders
 */

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Trebuie sa fiti autentificat']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $cart = isset($input['cart']) ? $input['cart'] : [];
        
        if (empty($cart)) {
            echo json_encode(['success' => false, 'message' => 'Cosul este gol']);
            exit();
        }
        
        $database = new Database();
        $conn = $database->getConnection();
        
        if (!$conn) {
            echo json_encode(['success' => false, 'message' => 'Eroare de conexiune la baza de date']);
            exit();
        }
        
        $total = 0;
        foreach ($cart as $item) {
            $total += floatval($item['price']) * (isset($item['quantity']) ? intval($item['quantity']) : 1);
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
                    $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
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
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Eroare: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metoda invalida']);
}
?>
