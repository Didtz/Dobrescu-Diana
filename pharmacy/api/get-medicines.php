<?php
/**
 * Get All Medicines API Endpoint
 * Returns all medicines from database with categories
 */

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Eroare de conexiune']);
        exit();
    }
    
    $query = "SELECT m.id, m.name, m.net_weight, m.short_description, m.description, m.requires_prescription, 
                     m.stock_quantity, m.price, m.side_effects,
                     GROUP_CONCAT(c.name SEPARATOR ',') as categories 
              FROM medicines m 
              LEFT JOIN medicine_categories mc ON m.id = mc.medicine_id 
              LEFT JOIN categories c ON mc.category_id = c.id 
              GROUP BY m.id 
              ORDER BY m.name";
    
    $result = $conn->query($query);
    
    $medicines = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $medicines[] = [
                'id' => intval($row['id']),
                'name' => $row['name'],
                'net_weight' => $row['net_weight'],
                'short_description' => $row['short_description'],
                'description' => $row['description'],
                'requires_prescription' => $row['requires_prescription'] == 1,
                'stock_quantity' => intval($row['stock_quantity']),
                'price' => floatval($row['price']),
                'side_effects' => $row['side_effects'] ? explode(',', $row['side_effects']) : [],
                'categories' => $row['categories'] ? explode(',', $row['categories']) : []
            ];
        }
    }
    
    echo json_encode(['success' => true, 'medicines' => $medicines]);
    $conn->close();
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Eroare: ' . $e->getMessage()]);
}
?>
