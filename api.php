<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { header('Access-Control-Allow-Methods: GET, POST, DELETE'); header('Access-Control-Allow-Headers: Content-Type'); exit; }
try{
  $dbPath = __DIR__.'/students.db';
  $db = new PDO('sqlite:'.$dbPath);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->exec("CREATE TABLE IF NOT EXISTS students (id INTEGER PRIMARY KEY AUTOINCREMENT, nume TEXT, an INTEGER, media REAL)");
  $check = $db->query('SELECT COUNT(*) as cnt FROM students')->fetch(PDO::FETCH_ASSOC);
  if($check['cnt'] == 0){
    $db->exec("INSERT INTO students(nume, an, media) VALUES ('Dorel', 2, 8.45)");
    $db->exec("INSERT INTO students(nume, an, media) VALUES ('Gigel', 3, 7.93)");
    $db->exec("INSERT INTO students(nume, an, media) VALUES ('DOBRESCU', 1, 9.00)");
  }
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $db->query('SELECT id, nume, an, media FROM students ORDER BY id DESC');
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['students'=>$students]);
    exit;
  }
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) $data = $_POST;
    $nume = trim($data['nume'] ?? '');
    $an = isset($data['an']) ? intval($data['an']) : null;
    $media = isset($data['media']) ? floatval($data['media']) : null;
    if ($nume === '' || !$an || $an<1 || $an>4 || $media === null || $media < 0 || $media>10) {
      http_response_code(400);
      echo json_encode(['ok'=>false,'mesaj'=>'date invalide']);
      exit;
    }
    $stmt = $db->prepare('INSERT INTO students(nume, an, media) VALUES(?, ?, ?)');
    $stmt->execute([$nume, $an, $media]);
    $id = $db->lastInsertId();
    echo json_encode(['ok'=>true,'student'=>['id'=>$id,'nume'=>$nume,'an'=>$an,'media'=>$media]]);
    exit;
  }
  if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = isset($data['id']) ? intval($data['id']) : null;
    if (!$id) {
      http_response_code(400);
      echo json_encode(['ok'=>false,'mesaj'=>'id necesar']);
      exit;
    }
    $stmt = $db->prepare('DELETE FROM students WHERE id=?');
    $stmt->execute([$id]);
    echo json_encode(['ok'=>true]);
    exit;
  }
  http_response_code(405);
  echo json_encode(['ok'=>false,'mesaj'=>'metoda nepermisa']);
} catch(Exception $e){
  http_response_code(500);
  echo json_encode(['ok'=>false,'mesaj'=>$e->getMessage()]);
}
