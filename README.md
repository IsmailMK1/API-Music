# API Music 🎵

API REST développée en PHP avec le framework Slim, permettant de gérer des artistes, des albums et des notes musicales.

## 1. Présentation du projet

Ce projet permet d'effectuer des opérations CRUD (Create, Read, Update, Delete) sur une base de données musicale MySQL.

L'API utilise une authentification JWT (JSON Web Token) pour sécuriser certaines opérations.

### Technologies utilisées

- PHP 8.2
- Slim Framework
- MySQL
- PDO
- Composer
- JWT (JSON Web Token)
- Postman
- PhpStorm
- Alwaysdata (hébergement)

## 2. URL de l'API

**API en ligne :**

https://ismailmk.alwaysdata.net

**API locale :**

http://localhost:8080

Les endpoints présentés ci-dessous sont à ajouter à l'URL de base.

## 3. Endpoints

### Artistes

| Méthode | Endpoint | Description | JWT |
|---|---|---|---|
| GET | `/GetAllArtist` | Récupérer tous les artistes | Non |
| GET | `/getArtistById/{id}` | Récupérer un artiste par ID | Non |
| GET | `/getArtistsByYear/{annee}` | Récupérer les artistes par année | Non |
| POST | `/addArtist` | Ajouter un artiste | Oui |
| PUT | `/updateArtist/{id}` | Modifier un artiste | Oui |
| DELETE | `/deleteArtist/{id}` | Supprimer un artiste | Oui |

### Albums

| Méthode | Endpoint | Description | JWT |
|---|---|---|---|
| GET | `/GetAllAlbums` | Récupérer tous les albums | Non |
| GET | `/getAlbumById/{id}` | Récupérer un album par ID | Non |
| GET | `/getAlbumsByArtist/{id}` | Récupérer les albums d'un artiste | Non |
| POST | `/addAlbum` | Ajouter un album | À vérifier |
| PUT | `/updateAlbum/{id}` | Modifier un album | À vérifier |
| DELETE | `/deleteAlbum/{id}` | Supprimer un album | À vérifier |

### Notes (Ratings)

| Méthode | Endpoint | Description | JWT |
|---|---|---|---|
| GET | `/GetAllRatings` | Récupérer toutes les notes | Non |
| GET | `/getRatingById/{id}` | Récupérer une note par ID | Non |
| GET | `/getRatingsByAlbum/{id}` | Récupérer les notes d'un album | Non |

### Authentification JWT

| Méthode | Endpoint | Description |
|---|---|---|
| POST | `/login` | Générer un token JWT |
| GET | `/protected` | Tester une route protégée |

## 4. Authentification

Certaines routes nécessitent un token JWT.

### Générer un token

**Méthode :** `POST`

**Endpoint :** `/login`

Une fois l'authentification réussie, l'API retourne un token JWT.

Exemple de réponse :

```json
{
  "token": "eyJ..."
}
```

### Utiliser le token

Dans Postman :

1. Ouvrir l'onglet **Authorization**.
2. Sélectionner **Bearer Token**.
3. Coller le token JWT obtenu.
4. Envoyer la requête.

Postman ajoute automatiquement l'en-tête :

```http
Authorization: Bearer <TOKEN_JWT>
```

### Vérification de la sécurité

**Sans token :**

```http
GET /protected
```

Réponse :

```json
{
  "error": "Unauthorized"
}
```

Statut HTTP : `401 Unauthorized`.

**Avec un token valide :**

```http
GET /protected
```

Statut HTTP : `200 OK`.

L'API autorise alors l'accès à la route protégée.

## 5. Exemples de requêtes

### Récupérer tous les artistes

```http
GET /GetAllArtist
```

### Récupérer les albums d'un artiste

```http
GET /getAlbumsByArtist/4
```

Exemple de réponse :

```json
[
  {
    "idAlbums": 5,
    "Titre": "SoloSun",
    "Artist_idArtist": 4
  },
  {
    "idAlbums": 6,
    "Titre": "Sunrise",
    "Artist_idArtist": 4
  }
]
```

### Récupérer une note

```http
GET /getRatingById/8
```

Exemple de réponse :

```json
{
  "idRatings": 8,
  "Grade": "5 Star",
  "Albums_idAlbums": 5
}
```

### Ajouter un artiste

```http
POST /addArtist
```

**Authorization :** Bearer Token

**Body :** `x-www-form-urlencoded`

| Champ | Exemple |
|---|---|
| Name | TestProf |
| Annee | 2026 |
| Description | Test creation JWT |

Exemple de réponse obtenue :

```json
{
  "message": "Artiste ajouté avec succès",
  "idArtist": "18"
}
```

Statut HTTP : `201 Created`.

### Modifier un artiste

```http
PUT /updateArtist/18
```

**Authorization :** Bearer Token

**Body :** `x-www-form-urlencoded`

| Champ | Exemple |
|---|---|
| Name | TestProfModifie |
| Annee | 2025 |
| Description | Modification avec JWT |

### Supprimer un artiste

```http
DELETE /deleteArtist/18
```

**Authorization :** Bearer Token

## 6. Structure du projet

```text
API-Music/
├── app/
│   ├── dependencies.php
│   ├── middleware.php
│   ├── repositories.php
│   ├── routes.php
│   └── settings.php
├── public/
│   └── index.php
├── src/
│   ├── Entity/
│   │   ├── Artist.php
│   │   ├── Album.php
│   │   └── Rating.php
│   ├── Middleware/
│   └── Repository/
│       ├── BaseRepository.php
│       ├── ArtistRepository.php
│       ├── AlbumRepository.php
│       └── RatingRepository.php
├── vendor/
└── composer.json
```

## 7. Base de données

La base de données MySQL contient trois tables principales :

**artists**

- `idArtist` : identifiant de l'artiste
- `Name` : nom de l'artiste
- `Annee` : année
- `Description` : description

**albums**

- `idAlbums` : identifiant de l'album
- `Titre` : titre de l'album
- `Artist_idArtist` : référence vers l'artiste

**ratings**

- `idRatings` : identifiant de la note
- `Grade` : note attribuée
- `Albums_idAlbums` : référence vers l'album

### Relations

- Un artiste peut posséder plusieurs albums.
- Un album est associé à un artiste.
- Un album peut posséder plusieurs notes.
- Une note est associée à un album.

## 8. Architecture

Le projet utilise une architecture organisée autour des composants suivants :

- **Routes** : définissent les endpoints HTTP.
- **Repositories** : exécutent les opérations sur la base de données.
- **BaseRepository** : centralise les opérations CRUD communes.
- **Entities** : représentent les artistes, albums et notes sous forme d'objets PHP.
- **Middleware JWT** : vérifie les tokens pour les routes protégées.

L'accès à MySQL s'effectue avec PDO et des requêtes préparées pour les paramètres utilisateurs.

## 9. Tests avec Postman

Les tests réalisés comprennent :

- Récupération des artistes, albums et notes.
- Recherche d'artistes par année.
- Recherche d'albums par artiste.
- Recherche de notes par album.
- Génération d'un token JWT.
- Refus d'accès à `/protected` sans token (`401`).
- Accès autorisé à `/protected` avec token (`200`).
- Création d'un artiste avec token (`201`).

Les autres opérations de modification et suppression peuvent être vérifiées avec Postman.

## 10. Auteur

Projet API Music — BTS CIEL, option Informatique et Réseaux.
