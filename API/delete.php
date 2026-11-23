<?php
require 'client_api.php';

$token = getToken('alice@example.com', 'motdepasse123');

if ($token === null) {
    die('Connexion impossible : token non obtenu.');
}

$url = API_URL . '/produits/3';

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_PROXY, '');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token
]);

$response = curl_exec($ch);

if ($response === false) {
    echo 'Erreur cURL : ' . curl_error($ch);
} else {
    echo '<pre>' . htmlspecialchars($response) . '</pre>';
}

curl_close($ch);