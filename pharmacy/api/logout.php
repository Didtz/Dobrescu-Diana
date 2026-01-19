<?php
/**
 * Logout API Endpoint
 * Destroys user session
 */

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

session_destroy();

echo json_encode(['success' => true, 'message' => 'Deconectat cu succes']);
?>
