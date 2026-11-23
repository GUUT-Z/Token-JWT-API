<?php
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
'api' => 'API REST Produits',
'version' => '1.0',
'endpoints' => [
'GET /api/produits' => 'Lister les produits',
'GET /api/produits/{id}' => 'Lire un produit',
'POST /api/produits' => 'Ajouter un produit',
'PUT /api/produits/{id}' => 'Modifier un produit',
'DELETE /api/produits/{id}' => 'Supprimer un produit'
]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
