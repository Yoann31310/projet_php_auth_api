# Auth API - Authentification Entraîneurs

Ceci est une API qui gère l'authentification des utilisateurs (Entraîneurs) en utilisant des jetons JWT pour le projetphp.

## Informations de Connexion
- **URL de Production (API)** : `https://alfred.alwaysdata.net/authapi.php`
- **Méthode** : `POST`
- **Base de données** : `alfred_api_auth` (Serveur `mysql-alfred.alwaysdata.net`)
- **Table** : `Entraineur` (Colonnes : `identifiant`, `mdp`, `nom`, `prenom`, etc.)

## Format des données (Entrée JSON)
L'API attend un objet JSON structuré comme suit :
```json
{
    "identifiant": "1573357",
    "password": "azertyuiop"
}
```

## Opérations effectuées par l'API
L'API effectue quatre opérations lors d'un appel :

1. Elle lit `php://input` pour extraire les données JSON et vérifie que `identifiant` et `password` existent.

2. Elle fait appel à la classe `EntraineurDAO` qui interroge la base de données. Elle récupère les informations de l'entraîneur pour voir si l'identifiant existe.

3. Elle utilise la fonction PHP `password_verify()` pour comparer le mot de passe saisi avec le hash stocké en BD.

4. Si tout est correct, elle construit un jeton JWT. Ce jeton est signé avec une clé secrète (`random`) et contient dans le payload les informations de session : 
- `id_entraineur`
- `identifiant`
- `nom`
- `prenom`
- `exp` : la date d'expiration (`exp`) fixée à 10 minutes (si non modifiée).

## Format de la Réponse (Sortie)
L'API renvoie toujours une réponse au format JSON avec un code de statut HTTP approprié :

### Succès (Statut 200)
```json
{
    "status_code": 200,
    "status_message": "Authentification réussie",
    "data": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZF9lbnRyYWluZXVyIjoiMSIs..."
}
```
> Note : Le champ `data` contient le jeton JWT complet à utiliser pour les requêtes futures. Il contient donc aussi dans son payload toutes les informations relatives à l'entraineur authentifié.

### Échec (Statut 401 ou 400)
```json
{
    "status_code": 401,
    "status_message": "Login ou mot de passe incorrect.",
    "data": null
}
```
