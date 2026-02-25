<?php
// Paramètres de connexion
$serveur = 'localhost';
$nom_bdd = 'r401_api';
$utilisateur = 'root';
$mot_de_passe = '';

try {
    $pdo = new PDO("mysql:host=$serveur;dbname=$nom_bdd;charset=utf8", $utilisateur, $mot_de_passe);
} catch (Exception $erreur) {
    die('Erreur de connexion : ' . $erreur->getMessage());
}
?>