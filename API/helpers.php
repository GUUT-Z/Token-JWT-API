<?php
require_once 'config.php';
require_once 'jwt.php';
// Envoie une réponse JSON avec un code HTTP, puis arrête le script
function sendResponse($code, $data)
{
http_response_code($code);
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit;
}
// Récupère le token dans l’en-tête « Authorization: Bearer xxx »
function getBearerToken()
{
$header = $_SERVER['HTTP_AUTHORIZATION'] ??
$_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
if ($header === '' && function_exists('getallheaders')) {
foreach (getallheaders() as $name => $value) {
if (strtolower($name) === 'authorization') {
$header = $value;
}
}
}
if (preg_match('/^Bearer\s+(\S+)$/i', $header, $matches)) {
return $matches[1];
}
return null;
}
// Exige un token valide : renvoie le payload ou arrête le script avec un 401
function requireAuth()
{
$token = getBearerToken();
if ($token === null) {
header('WWW-Authenticate: Bearer');
sendResponse(401, ['status' => 0, 'status_message' => 'Token manquant : en-
tête Authorization: Bearer requis.']);
}
$payload = verifyJWT($token, JWT_SECRET);
if ($payload === false) {
header('WWW-Authenticate: Bearer error="invalid_token"');
sendResponse(401, ['status' => 0, 'status_message' => 'Token invalide ou
expiré.']);
}
return $payload;
}