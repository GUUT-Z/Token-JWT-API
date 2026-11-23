<?php
include('db_connect.php');
include('helpers.php');
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
header('Allow: POST');
sendResponse(405, ['status' => 0, 'status_message' => 'Méthode non autorisée :
utilisez POST.']);
}
$email = trim($_POST['email'] ?? '');
$mdp = $_POST['mot_de_passe'] ?? '';
try {
$stmt = mysqli_prepare($conn, 'SELECT id, nom, email, mot_de_passe FROM client
WHERE email = ?');
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$client = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
// Message identique que l’email soit inconnu ou le mot de passe faux :
// on ne révèle pas quels emails possèdent un compte
if (!$client || !password_verify($mdp, $client['mot_de_passe'])) {
sendResponse(401, ['status' => 0, 'status_message' => 'Email ou mot de passe
incorrect.']);
}
$now = time();
$payload = [
'iss' => JWT_ISSUER,
'sub' => (string) $client['id'],
'email' => $client['email'],
'iat' => $now,
'exp' => $now + JWT_TTL
];
sendResponse(200, [
'status' => 1,
'token_type' => 'Bearer',
'expires_in' => JWT_TTL,
'token' => generateJWT($payload, JWT_SECRET)
]);
} catch (mysqli_sql_exception $e) {
sendResponse(500, ['status' => 0, 'status_message' => 'Erreur serveur.']);
}