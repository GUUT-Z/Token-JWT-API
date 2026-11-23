<?php
// Clé secrète servant à signer les tokens.
// Pour en générer une, dans un terminal : php -r "echo bin2hex(random_bytes(32));"
define('JWT_SECRET', 'REMPLACEZ_PAR_VOTRE_CLE_ALEATOIRE_DE_64_CARACTERES');
define('JWT_TTL', 10); // durée de validité d’un token : 1 heure
define('JWT_ISSUER', 'api-produits'); // émetteur du token