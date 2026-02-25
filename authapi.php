<?php
// Imports des fichiers
require_once 'connexionDB.php';
require_once 'jwt_utils.php';

// Fonction pour envoyer une réponse au client
function deliver_response($code_statut, $message_statut, $donnees = null)
{
    // Définit le code de statut HTTP 
    http_response_code($code_statut);  // Utilise un message standardisé en fonction du code HTTP
    // header("HTTP/1.1 $status_code $status_message"); //Pour personnaliser le message associé au code HTTP  

    // Headers CORS complets (autorise toutes les origines et les méthodes) 
    header("Access-Control-Allow-Origin: *");                                           // "*" car tout le monde a le droit de l'appeler
    header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");     // Toutes les méthodes qu'on autorise
    header("Access-Control-Allow-Headers: Content-Type");                               // Pour dire que j'envoie du JSON 
    header("Content-Type: application/json; charset=utf-8");                            // Format de la réponse du json 

    $reponse['status_code'] = $code_statut;
    $reponse['status_message'] = $message_statut;
    $reponse['data'] = $donnees;

    // Mapping de la réponse au format JSON 
    $json_response = json_encode($reponse);
    if ($json_response === false)
        die('json encode ERROR : ' . json_last_error_msg());
    // Affichage de la réponse (Retourné au client) 
    echo $json_response;
}

// Gestion du CORS
$methode = $_SERVER['REQUEST_METHOD'];

if ($methode == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    http_response_code(204);
    exit;
}

// Authentification se fait obligatoirement par une requête POST 
if ($methode != 'POST') {
    deliver_response(405, "Méthode non autorisée. Il faut utiliser POST.");
    exit;
}

// On lit le corps de la requête
$donnees_brutes = file_get_contents('php://input');

// On transforme le JSON reçu en tableau associatif PHP
$data = json_decode($donnees_brutes, true);

// On vérifie que le login et le mot de passe sont bien présents
if (!isset($data['login']) || !isset($data['password'])) {
    deliver_response(400, "Erreur : Login ou mot de passe manquant.");
    exit;
}

$login_saisi = $data['login'];
$mdp_saisi = $data['password'];


// On cherche l'utilisateur qui correspond au login donné
$query = $pdo->prepare("SELECT * FROM user WHERE login = :login");
$query->execute([':login' => $login_saisi]);

$user = $query->fetch(PDO::FETCH_ASSOC);

// On vérifie que user != false (existe) et on compare le mdp en déhashant
if ($user && password_verify($mdp_saisi, $user['password'])) {

    // On génère le jeton
    $headers = array('algo' => 'HS256', 'type' => 'JWT');         // header

    $payload = array(                                           // payload
        'login' => $user['login'],      // On met le login 
        'exp' => time() + 60            // Le jeton expire dans 60s
    );

    // On appelle la fonction fournie dans jwt_utils.php pour créer la chaîne
    $jwt = generate_jwt($headers, $payload, 'random'); // La clé secrète (signature) = 'random'

    // On renvoie le code 200 au client avec le jeton dans le champ "data"
    deliver_response(200, "Authentification réussie", $jwt);

} else {
    // Si login inexistant ou mdp incorrect
    deliver_response(401, "Login ou mot de passe incorrect.");
}

?>