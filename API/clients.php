
<?php

include('db_connect.php');
include('helpers.php');

header('Content-Type: application/json; charset=utf-8');

// POST /clients : créer un compte (route publique)
function registerClient()
{
    global $conn;

    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';

    if (
        $nom === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($mdp) < 8
    ) {
        sendResponse(400, [
            'status' => 0,
            'status_message' => 'Données invalides : nom, email valide et mot de passe (8 caractères minimum) obligatoires.'
        ]);
    }

    // Le mot de passe n'est JAMAIS stocké en clair
    $hash = password_hash($mdp, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare(
        $conn,
        'INSERT INTO client (nom, email, mot_de_passe, dateCreation)
         VALUES (?, ?, ?, CURDATE())'
    );

    mysqli_stmt_bind_param($stmt, 'sss', $nom, $email, $hash);
    mysqli_stmt_execute($stmt);

    sendResponse(201, [
        'status' => 1,
        'status_message' => 'Compte créé avec succès.',
        'id' => mysqli_insert_id($conn)
    ]);
}

// GET /clients/me : profil du client authentifié (route protégée)
function getProfile()
{
    global $conn;

    // Arrête le script avec un 401 si le token est invalide
    $payload = requireAuth();

    // L'identité vient du token, pas de l'URL
    $id = (int) $payload['sub'];

    $stmt = mysqli_prepare(
        $conn,
        'SELECT id, nom, email, dateCreation
         FROM client
         WHERE id = ?'
    );

    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    $client = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($client) {
        sendResponse(200, $client);
    } else {
        sendResponse(404, [
            'status' => 0,
            'status_message' => 'Client introuvable.'
        ]);
    }
}

// Gestion des routes
try {

    switch ($_SERVER['REQUEST_METHOD']) {

        case 'POST':
            registerClient();
            break;

        case 'GET':

            if (($_GET['action'] ?? '') !== 'me') {
                sendResponse(404, [
                    'status' => 0,
                    'status_message' => 'Route inconnue. Utilisez /clients/me.'
                ]);
            }

            getProfile();
            break;

        default:
            header('Allow: GET, POST');

            sendResponse(405, [
                'status' => 0,
                'status_message' => 'Méthode non autorisée.'
            ]);
    }

} catch (mysqli_sql_exception $e) {

    if ($e->getCode() === 1062) {
        // Violation de la contrainte UNIQUE sur email
        sendResponse(409, [
            'status' => 0,
            'status_message' => 'Cet email est déjà utilisé.'
        ]);
    }

    sendResponse(500, [
        'status' => 0,
        'status_message' => 'Erreur serveur.'
    ]);
}
