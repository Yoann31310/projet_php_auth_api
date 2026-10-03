<?php
// Pour se connecter à la base de données 
// (les identifiants ne sont plus écrits dans le code : voir README.md)

class Database
{
    private static $instance = null;
    private $connexion;

    // Constructeur privé car classe Singleton
    private function __construct()
    {
        try {
            // Configuration : variables d'environnement, avec les valeurs par défaut d'un XAMPP local
            $host = getenv('DB_HOST') ?: 'localhost';
            $bd = getenv('DB_NAME') ?: 'equipe_sport_auth';
            $utilisateur = getenv('DB_USER') ?: 'root';
            $mdp = getenv('DB_PASSWORD') ?: '';

            $this->connexion = new PDO("mysql:host=$host;dbname=$bd;charset=utf8", $utilisateur, $mdp);

            // On force PDO à afficher les erreurs SQL (très important pour les tests)
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
    }

    // Servira pour après, pour accéder à l'instance de la BD existante
    public static function getInstance()
    {
        if (self::$instance === null)
            self::$instance = new Database();
        return self::$instance->connexion;
    }
}