<?php
/**
 * Test Connection to Database
 * Use this file to verify the database connection is working
 * Access: http://localhost/pharmacy/test-connection.php
 */

require_once 'config/database.php';

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>Test Conexiune Baza de Date</title>";
echo "<style>";
echo "body { font-family: Arial; margin: 20px; background: #f5f5f5; }";
echo ".container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo "h1 { color: #333; border-bottom: 2px solid #ff4081; padding-bottom: 10px; }";
echo ".success { background: #c8e6c9; border-left: 4px solid #4caf50; padding: 10px; margin: 10px 0; }";
echo ".error { background: #ffcdd2; border-left: 4px solid #f44336; padding: 10px; margin: 10px 0; }";
echo ".info { background: #bbdefb; border-left: 4px solid #2196f3; padding: 10px; margin: 10px 0; }";
echo ".test { margin: 15px 0; }";
echo ".test h3 { margin: 10px 0; color: #666; }";
echo "code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }";
echo "table { width: 100%; border-collapse: collapse; margin: 10px 0; }";
echo "table th, table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }";
echo "table th { background: #ff4081; color: white; }";
echo "</style>";
echo "</head>";
echo "<body>";
echo "<div class='container'>";
echo "<h1>🧪 Test Conexiune Baza de Date - Farmacie</h1>";

// Test 1: Connection
echo "<div class='test'>";
echo "<h3>Test 1: Conectare la MySQL</h3>";

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn && !$conn->connect_error) {
        echo "<div class='success'>✅ Conexiune reusita la MySQL!</div>";
        echo "<div class='info'>Host: localhost | Port: 3306 | Database: pharmacy</div>";
    } else {
        echo "<div class='error'>❌ Eroare conexiune: " . ($conn ? $conn->connect_error : "Unknown error") . "</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Exceptie: " . $e->getMessage() . "</div>";
}

echo "</div>";

// Test 2: Check Tables
echo "<div class='test'>";
echo "<h3>Test 2: Verificare Tabele</h3>";

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $tables = ['users', 'medicines', 'categories', 'medicine_categories', 'orders', 'order_items'];
    
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "<div class='success'>✅ Tabel <code>$table</code> existe</div>";
        } else {
            echo "<div class='error'>❌ Tabel <code>$table</code> NU EXISTA</div>";
        }
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "<div class='error'>❌ Exceptie: " . $e->getMessage() . "</div>";
}

echo "</div>";

// Test 3: Count Records
echo "<div class='test'>";
echo "<h3>Test 3: Numărul de Înregistrări</h3>";

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $queries = [
        'users' => 'SELECT COUNT(*) as count FROM users',
        'medicines' => 'SELECT COUNT(*) as count FROM medicines',
        'categories' => 'SELECT COUNT(*) as count FROM categories',
        'orders' => 'SELECT COUNT(*) as count FROM orders'
    ];
    
    foreach ($queries as $table => $query) {
        $result = $conn->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $count = $row['count'];
            echo "<div class='info'>📊 Tabel <strong>$table</strong>: <strong>$count</strong> înregistrări</div>";
        }
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "<div class='error'>❌ Exceptie: " . $e->getMessage() . "</div>";
}

echo "</div>";

// Test 4: Sample Medicines
echo "<div class='test'>";
echo "<h3>Test 4: Medicamente Preîncărcate</h3>";

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $result = $conn->query("SELECT id, name, price, requires_prescription, stock_quantity FROM medicines LIMIT 5");
    
    if ($result && $result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Nume</th><th>Preț</th><th>Rețetă</th><th>Stoc</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            $prescription = $row['requires_prescription'] ? '✅ Da' : '❌ Nu';
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['price'] . " Lei</td>";
            echo "<td>" . $prescription . "</td>";
            echo "<td>" . $row['stock_quantity'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        echo "<div class='success'>✅ Medicamentele se încarcă corect din baza de date!</div>";
    } else {
        echo "<div class='error'>❌ Nu sunt medicamente în baza de date</div>";
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "<div class='error'>❌ Exceptie: " . $e->getMessage() . "</div>";
}

echo "</div>";

// Test 5: API Endpoints Check
echo "<div class='test'>";
echo "<h3>Test 5: API Endpoints</h3>";

$endpoints = [
    'api/login.php' => 'POST - Autentificare utilizatori',
    'api/signup.php' => 'POST - Înregistrare utilizatori',
    'api/get-medicines.php' => 'GET - Obține medicamente',
    'api/checkout.php' => 'POST - Plasare comandă',
    'api/check-session.php' => 'GET - Verifică sesiune',
    'api/logout.php' => 'POST - Deconectare'
];

foreach ($endpoints as $file => $description) {
    if (file_exists($file)) {
        echo "<div class='success'>✅ <code>$file</code> - $description</div>";
    } else {
        echo "<div class='error'>❌ <code>$file</code> - LIPSIT!</div>";
    }
}

echo "</div>";

// Test 6: Session Test
echo "<div class='test'>";
echo "<h3>Test 6: Sesiuni PHP</h3>";

session_start();
$_SESSION['test_value'] = 'test_successful';

if (isset($_SESSION['test_value']) && $_SESSION['test_value'] === 'test_successful') {
    echo "<div class='success'>✅ Sesiunile PHP functioneaza corect</div>";
    echo "<div class='info'>Session ID: " . session_id() . "</div>";
} else {
    echo "<div class='error'>❌ Eroare sesiuni PHP</div>";
}

echo "</div>";

// Summary
echo "<div style='margin-top: 30px; padding: 15px; background: #fff3e0; border-left: 4px solid #ff9800; border-radius: 4px;'>";
echo "<h3>📋 Rezumat</h3>";
echo "<p>Dacă toate testele sunt marcate cu ✅, proiectul este gata să ruleze!</p>";
echo "<p><strong>Următorii pași:</strong></p>";
echo "<ol>";
echo "<li>Accesează: <code>http://localhost/pharmacy/index.html</code></li>";
echo "<li>Creează un utilizator nou prin înregistrare</li>";
echo "<li>Autentifică-te cu email-ul și parola</li>";
echo "<li>Vizitează pagina medicamentelor</li>";
echo "</ol>";
echo "</div>";

echo "</div>";
echo "</body>";
echo "</html>";
?>
