<?php
$url = 'http://127.0.0.1/API/produits';
$data = [
'nom' => 'Crayon',
'description' => 'Crayon à papier 2H',
'prix' => '2.25',
'numCategorie' => '1'
];
$options = [
'http' => [
'header' => ['Content-Type: application/x-www-form-urlencoded'],
'method' => 'POST',
'content' => http_build_query($data),
'ignore_errors' => true // permet de lire la réponse même en cas d’erreur 4xx/5xx
]
];
$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === false) {
die('Impossible de joindre l’API.');
}
echo '<pre>' . htmlspecialchars($result) . '</pre>';