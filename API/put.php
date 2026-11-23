<?php
require 'client_api.php';
// Utilisez le client créé à la consigne 3 (identifiants en dur : acceptable en TPuniquement)
$token = getToken('alice@example.com', 'motdepasse123');
if ($token === null) {
die('Connexion impossible : token non obtenu.');
}
$data = [
'nom' => 'MAC',
'description' => 'Ordinateur portable',
'prix' => '9658',
'numCategorie' => '2'
];
$ch = curl_init(API_URL . '/produits/1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
'Content-Type: application/x-www-form-urlencoded',
'Authorization: Bearer ' . $token]);
$response = curl_exec($ch);
if ($response === false) {
echo 'Erreur cURL : ' . curl_error($ch);
} else {
echo '<pre>' . htmlspecialchars($response) . '</pre>';
}

