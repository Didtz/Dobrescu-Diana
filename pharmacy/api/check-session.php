<?php
/**
 * Check Session API Endpoint
 * Verifies if user is logged in
 */

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo json_encode([
        'logged_in' => true,
        'user' => [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'first_name' => $_SESSION['first_name'] ?? null,
            'last_name' => $_SESSION['last_name'] ?? null,
            'role' => $_SESSION['role'] ?? null
        ]
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
?>
