# Service d'Authentification - Auth API

## Introduction
Cette API gère l'authentification des utilisateurs (Entraîneurs) en utilisant des jetons JWT. Elle permet de sécuriser l'accès aux différentes ressources du projet.

## Informations de Connexion
- **URL** : `https://alfred.alwaysdata.net/authapi.php`
- **Méthodes autorisées** : `POST` et `GET`
- **Base de données** : `alfred_api_auth` (Serveur `mysql-alfred.alwaysdata.net`)
- **Utilisateur** : `alfred`
- **Mot de passe** : `azertyuiop.@`

## Fonctionnement des Méthodes
### 1. Authentification (POST)
Utilisée pour la connexion initiale. L'API attend un objet JSON contenant l'identifiant et le mot de passe :
```json
{
    "identifiant": "1573357",
    "password": "azertyuiop"
}
```
**Processus** :
1. Recherche l'entraîneur dans la table `Entraineur`.
2. Vérifie le mot de passe en déhashant avec `password_verify()`.
3. Génère un jeton JWT signé avec une clé secrète (`random`) contenant les informations de l'entraîneur et une date d'expiration (10 minutes).
4. Retourne le jeton dans le champ `data` de la réponse.

### 2. Vérification de Jeton (GET)
Cette méthode est cruciale pour l'interopérabilité entre les services. Elle permet de vérifier si un jeton JWT est toujours valide et n'a pas expiré.
- **Utilisation** : Doit être accompagnée d'un header `Authorization: Bearer <votre_jeton>`.
- **Rôle majeur** : Elle est appelée par l'API de gestion sportive (`api_gestion`) à chaque requête entrante pour s'assurer que l'utilisateur est bien authentifié avant de lui donner accès aux données des joueurs ou des matchs.

---

## Format de la Réponse
L'API renvoie toujours un objet JSON structuré ainsi :
- `status_code` : Code HTTP (200, 401, 400, etc.).
- `status_message` : Description textuelle du résultat.
- `data` : Contient le jeton (pour POST) ou reste à `null` pour les autres cas.

## Liens et Dépendances
- Ce service constitue le pilier de sécurité du projet.
- Il est le point de passage obligatoire pour le Frontend avant toute autre action.
- Il sert de "gardien" pour l'API de gestion sportive qui délègue la vérification des tokens à cette méthode GET.

---

## Piste d'amélioration :
- Actuellement, le mot de passe de l'utilisateur est stocké en clair dans la base de données. Il serait préférable de le stocker de manière sécurisée en utilisant un algorithme de hachage / Cryptage, ou simplement un .env(). On pourrait aussi essayer de faire en sorte que la clé change de manière dynamique.


PS : On peut tester l'api via Swagger UI en allant sur l'adresse : https://alfred.alwaysdata.net/docs