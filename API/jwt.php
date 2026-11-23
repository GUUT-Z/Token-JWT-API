<?php
function base64url_encode($data)
{
return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
function base64url_decode($data)
{
return base64_decode(strtr($data, '-_', '+/'));
}
// Génère un JWT signé en HS256
function generateJWT($payload, $secret)
{
$header = ['alg' => 'HS256', 'typ' => 'JWT'];
$h = base64url_encode(json_encode($header));
$p = base64url_encode(json_encode($payload));
$signature = base64url_encode(hash_hmac('sha256', $h . '.' . $p, $secret, true));
return $h . '.' . $p . '.' . $signature;
}
// Vérifie un JWT : retourne le payload (tableau) s’il est valide, sinon false
function verifyJWT($jwt, $secret)
{
$parts = explode('.', $jwt);
if (count($parts) !== 3) {
return false;
}
list($h, $p, $s) = $parts;
// 1. L’algorithme est imposé par le serveur, on ignore ce que réclame le token
$header = json_decode(base64url_decode($h), true);
if (!is_array($header) || ($header['alg'] ?? '') !== 'HS256') {
return false;
}
// 2. Recalcul de la signature et comparaison à temps constant
$expected = base64url_encode(hash_hmac('sha256', $h . '.' . $p, $secret, true));
if (!hash_equals($expected, $s)) {
return false;
}
// 3. Le payload doit être lisible et le token non expiré
$payload = json_decode(base64url_decode($p), true);
if (!is_array($payload) || !isset($payload['exp']) || $payload['exp'] < time()) {
return false;
}
return $payload;
}