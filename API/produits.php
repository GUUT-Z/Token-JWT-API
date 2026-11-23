<?php
include('db_connect.php');
include('helpers.php'); // contient désormais sendResponse()
header('Content-Type: application/json; charset=utf-8');

// Se connecter à la base de données
include('db_connect.php');
header('Content-Type: application/json; charset=utf-8');
$request_method = $_SERVER['REQUEST_METHOD'];
// Envoie une réponse JSON avec un code HTTP, puis arrête le script

function getProducts()
{
global $conn;
$result = mysqli_query($conn, 'SELECT * FROM produit');
$response = mysqli_fetch_all($result, MYSQLI_ASSOC);
sendResponse(200, $response);
}

function AddProduct()
{
global $conn;
$nom = trim($_POST['nom'] ?? '');
$description = trim($_POST['description'] ?? '');
$prix = $_POST['prix'] ?? '';
$numCategorie = $_POST['numCategorie'] ?? '';
// Validation minimale des données reçues
if ($nom === '' || !is_numeric($prix) || !is_numeric($numCategorie)) {
sendResponse(400, ['status' => 0, 'status_message' => 'Données invalides :
nom, prix et numCategorie sont obligatoires.']);
}
$prix = (float) $prix;
$numCategorie = (int) $numCategorie;
$stmt = mysqli_prepare($conn,
'INSERT INTO produit (nom, description, prix, numCategorie, dateCreation,
dateModification)
VALUES (?, ?, ?, ?, CURDATE(), CURDATE())');
mysqli_stmt_bind_param($stmt, 'ssdi', $nom, $description, $prix, $numCategorie);
mysqli_stmt_execute($stmt);
sendResponse(201, [
'status' => 1,
'status_message' => 'Produit ajouté avec succès.',
'id' => mysqli_insert_id($conn)
]);
}

function getProduct($id)
{
global $conn;
$stmt = mysqli_prepare($conn, 'SELECT * FROM produit WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);
if ($product) {
sendResponse(200, $product);
} else {
sendResponse(404, ['status' => 0, 'status_message' => 'Produit
introuvable.']);
}
}

function updateProduct($id)
{
global $conn;
$_PUT = []; // tableau qui va contenir les données reçues
parse_str(file_get_contents('php://input'), $_PUT);
$nom = trim($_PUT['nom'] ?? '');
$description = trim($_PUT['description'] ?? '');
$prix = $_PUT['prix'] ?? '';
$numCategorie = $_PUT['numCategorie'] ?? '';
if ($nom === '' || !is_numeric($prix) || !is_numeric($numCategorie)) {
sendResponse(400, ['status' => 0, 'status_message' => 'Données invalides :
nom, prix et numCategorie sont obligatoires.']);
}
$prix = (float) $prix;
$numCategorie = (int) $numCategorie;
$stmt = mysqli_prepare($conn,
'UPDATE produit
SET nom = ?, description = ?, prix = ?, numCategorie = ?, dateModification =
CURDATE()
WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'ssdii', $nom, $description, $prix, $numCategorie,
$id);
mysqli_stmt_execute($stmt);
if (mysqli_stmt_affected_rows($stmt) > 0) {
sendResponse(200, ['status' => 1, 'status_message' => 'Produit mis à jour
avec succès.']);
} else {
sendResponse(404, ['status' => 0, 'status_message' => 'Produit introuvable ou
aucune modification.']);
}
}

function deleteProduct($id)
{
global $conn;
$stmt = mysqli_prepare($conn, 'DELETE FROM produit WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
if (mysqli_stmt_affected_rows($stmt) > 0) {
sendResponse(200, ['status' => 1, 'status_message' => 'Produit supprimé avec
succès.']);
} else {
sendResponse(404, ['status' => 0, 'status_message' => 'Produit
introuvable.']);
}
}

try {
switch ($request_method) {
case 'GET':
if (!empty($_GET['id'])) {
getProduct(intval($_GET['id']));
} else {
getProducts();
}
break;
case 'POST':
AddProduct();
break;
case 'PUT':
requireAuth(); // route protégée : token JWT obligatoire
$id = intval($_GET['id'] ?? 0);
if ($id === 0) {
sendResponse(400, ['status' => 0, 'status_message' => 'Identifiant manquant :
utilisez /produits/{id}.']);
}
updateProduct($id);
break;
case 'DELETE':
requireAuth(); // route protégée : token JWT obligatoire
$id = intval($_GET['id'] ?? 0);
if ($id === 0) {
sendResponse(400, ['status' => 0, 'status_message' => 'Identifiant manquant :
utilisez /produits/{id}.']);
}
deleteProduct($id);
break;
default:
header('Allow: GET, POST, PUT, DELETE');
sendResponse(405, ['status' => 0, 'status_message' => 'Méthode non
autorisée.']);
}
} catch (mysqli_sql_exception $e) {
sendResponse(500, ['status' => 0, 'status_message' => 'Erreur serveur : ' .
$e->getMessage()]);
}


