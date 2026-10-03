# API d'authentification (PHP, JWT)

> **Projet étudiant** réalisé à l'IUT de Toulouse (BUT Informatique, 2e année, module R4.01 « Architecture logicielle »). C'est la première brique d'un projet de gestion d'équipe de handball. Une version corrigée et regroupée avec les deux autres parties (API de gestion et interface web) est dans le dépôt [`projetphpgestionjoueurs-matchs`](https://github.com/Yoann31310/projetphpgestionjoueurs-matchs). Ce dépôt garde l'historique d'origine de ce service.

Petite API REST en PHP qui connecte un entraîneur (identifiant et mot de passe) et lui remet un **jeton JWT**. Les autres services, comme l'API de gestion, interrogent cette API pour savoir si un jeton est encore valable.

## Ce que fait l'API

Une seule adresse, `authapi.php`, avec deux usages :

| Méthode | Rôle | Réponse |
|---|---|---|
| `POST` | **Connexion.** Corps JSON : `{"identifiant": "coach", "password": "demo1234"}` | `200` et le jeton dans `data`, ou `401` si l'identifiant ou le mot de passe est faux |
| `GET` | **Vérification d'un jeton.** En-tête `Authorization: Bearer <jeton>` | `200` « Jeton valide » ou `401` |

Déroulement d'une connexion :
1. recherche de l'entraîneur dans la table `Entraineur` ;
2. vérification du mot de passe avec `password_verify()` (il est stocké haché) ;
3. création d'un jeton JWT signé (HS256) avec les informations de l'entraîneur, valable 10 minutes.

Toutes les réponses ont la même forme : `status_code`, `status_message`, `data`.

La documentation interactive (Swagger UI) est dans `docs/index.html`, à partir du fichier `docs/openapi.yaml`.

## Technologies

PHP 8 (PDO, cURL), MySQL / MariaDB, jetons JWT écrits à la main dans `jwt_utils.php` (HMAC SHA-256), documentation OpenAPI 3.

## Base de données

Le script [`db/schema.sql`](db/schema.sql) crée la base `equipe_sport_auth` avec une seule table, `Entraineur` (`Id_Entraineur`, `identifiant`, `mdp` haché, `nom`, `prenom`, `email`) et un compte de démonstration inventé :

| Identifiant | Mot de passe |
|---|---|
| `coach` | `demo1234` |

## Installation

Prérequis : PHP 8 avec l'extension `pdo_mysql`, et MySQL ou MariaDB (par exemple avec [XAMPP](https://www.apachefriends.org/)).

1. **Créer la base** :
   ```bash
   mysql -u root -p < db/schema.sql
   ```
2. **Régler la connexion à la base.** `connexionDB.php` lit les variables d'environnement `DB_HOST`, `DB_NAME`, `DB_USER` et `DB_PASSWORD`. Sans réglage, il se connecte à `localhost` avec `root` sans mot de passe (XAMPP).
3. **Lancer le serveur** depuis ce dossier :
   ```bash
   php -S 127.0.0.1:8001
   ```
4. **Tester** :
   ```bash
   curl -X POST http://127.0.0.1:8001/authapi.php \
     -H "Content-Type: application/json" \
     -d '{"identifiant":"coach","password":"demo1234"}'
   ```
   Puis, avec le jeton obtenu :
   ```bash
   curl http://127.0.0.1:8001/authapi.php -H "Authorization: Bearer <jeton>"
   ```

Ne jamais écrire de mot de passe de base de données dans le code ni dans ce dépôt.

## Organisation

```
authapi.php          point d'entrée (POST connexion, GET vérification)
jwt_utils.php        création et vérification des jetons
connexionDB.php      connexion PDO (une seule instance)
Classes/Entraineur.php   objet métier
DAO/EntraineurDAO.php    requêtes SQL
db/schema.sql        structure et compte de démonstration
docs/                documentation Swagger / OpenAPI
```

## Limites de cette version

Elles sont corrigées dans la version regroupée du dépôt `projetphpgestionjoueurs-matchs` :
- la clé qui signe les jetons est écrite dans le code (`random`) au lieu d'être secrète et longue ;
- les réponses autorisent tous les sites (`Access-Control-Allow-Origin: *`) ;
- aucun frein aux essais répétés de mot de passe.
