<?php
$server = 'localhost';
$username = 'root';
$password = '';
$db = 'api';
// Les erreurs MySQL déclenchent des exceptions (plus simple à gérer)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
$conn = mysqli_connect($server, $username, $password, $db);
mysqli_set_charset($conn, 'utf8mb4');
} catch (mysqli_sql_exception $e) {
http_response_code(500);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['status' => 0, 'status_message' => 'Connexion à la base
impossible.']);
exit;
}