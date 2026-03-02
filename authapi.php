<?php
// Imports des fichiers nécessaires
require_once 'connexionDB.php';
require_once 'jwt_utils.php';
require_once 'Classes/Entraineur.php';
require_once 'DAO/EntraineurDAO.php';

// Envoyer une réponse JSON au client
function deliver_response($code_statut, $message_statut, $donnees = null)
{
    http_response_code($code_statut);

    // Configuration des headers CORS et Type de contenu
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Content-Type: application/json; charset=utf-8");

    $reponse = [
        'status_code' => $code_statut,
        'status_message' => $message_statut,
        'data' => $donnees
    ];

    $json_response = json_encode($reponse);
    if ($json_response === false) {
        die('json encode ERROR : ' . json_last_error_msg());
    }

    echo $json_response;
}

// Gestion des requêtes de pré-vérification CORS
$methode = $_SERVER['REQUEST_METHOD'];
if ($methode == 'OPTIONS') {
    deliver_response(204, "CORS Autorisé");
    exit;
}

// L'authentification veut QUE la méthode POST
if ($methode != 'POST') {
    deliver_response(405, "Méthode non autorisée. Il faut utiliser POST.");
    exit;
}

// Lecture et décodage des données JSON reçues
$donnees_brutes = file_get_contents('php://input');
$data = json_decode($donnees_brutes, true);

// On vérifie que l'identifiant et le password sont bien présents dans le JSON reçu
if (!isset($data['identifiant']) || !isset($data['password'])) {
    deliver_response(400, "Erreur : Identifiant ou mot de passe manquant.");
    exit;
}

$id_saisi = $data['identifiant'];
$mdp_saisi = $data['password'];

// Recherche de l'entraîneur via le DAO
$entraineur = Entraineur::verifier_identifiant($id_saisi);

// On vérifie que entraineur existe et on compare le mdp
if ($entraineur && $entraineur->verifier_mot_de_passe($mdp_saisi)) {
    $headers = array('algo' => 'HS256', 'type' => 'JWT');

    $payload = array(
        'id_entraineur' => $entraineur->get_id_entraineur(),
        'identifiant' => $entraineur->get_identifiant(),
        'nom' => $entraineur->get_nom(),
        'prenom' => $entraineur->get_prenom(),
        'exp' => time() + 600                                              // Expire dans 10min 
    );

    $signature = 'random';                                                  // La clé secrète 
    $jwt = generate_jwt($headers, $payload, $signature);
    deliver_response(200, "Authentification réussie", $jwt);
} else {
    deliver_response(401, "Login ou mot de passe incorrect.");
}
exit;
?>