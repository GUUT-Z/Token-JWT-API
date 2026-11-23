
<?php

const API_URL = 'http://localhost/api';

// Se connecte à l'API et retourne le token (ou null en cas d'échec)
function getToken($email, $motDePasse)
{
    $ch = curl_init(API_URL . '/login');

    // Désactiver le proxy
    curl_setopt($ch, CURLOPT_PROXY, '');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'email' => $email,
        'mot_de_passe' => $motDePasse
    ]));

    $response = curl_exec($ch);

    if ($response === false) {
        die('Erreur cURL : ' . curl_error($ch));
    }

    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($code !== 200) {
        die('Erreur HTTP ' . $code . ' : ' . $response);
    }

    $data = json_decode($response, true);

    curl_close($ch);

    return $data['token'] ?? null;
}
