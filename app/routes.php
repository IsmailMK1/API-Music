<?php

declare(strict_types=1);

use App\Middleware\JwtHelper;
use App\Middleware\JwtMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {

    // =====================================================
    // ACCUEIL
    // =====================================================

    $app->get('/', function (
        Request $request,
        Response $response
    ) {

        $response->getBody()->write(
            json_encode([
                'message' => 'API Music fonctionne'
            ])
        );

        return $response->withHeader(
            'Content-Type',
            'application/json'
        );
    });


    // =====================================================
    // LOGIN - CREATION DU TOKEN JWT
    // =====================================================

    $app->post('/login', function (
        Request $request,
        Response $response
    ) {

        $data = $request->getParsedBody();

        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        if (
            $username === 'SaintMichel' &&
            $password === 'ITcampus'
        ) {

            $token = JwtHelper::generateToken([
                'username' => $username
            ]);

            $response->getBody()->write(
                json_encode([
                    'token' => $token
                ])
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }

        $response->getBody()->write(
            json_encode([
                'error' => 'Identifiants incorrects'
            ])
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401);
    });


    // =====================================================
    // GET - TOUS LES ARTISTES
    // =====================================================

    $app->get('/GetAllArtist', function (
        Request $request,
        Response $response
    ) {

        $db = $this->get(PDO::class);

        $sql = "SELECT * FROM artists";

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $artists = $stmt->fetchAll();

        $response->getBody()->write(
            json_encode($artists)
        );

        return $response->withHeader(
            'Content-Type',
            'application/json'
        );
    });


    // =====================================================
    // GET - ARTISTE PAR ID
    // =====================================================

    $app->get('/getArtistById/{id}', function (
        Request $request,
        Response $response,
        array $args
    ) {

        $db = $this->get(PDO::class);

        $id = $args['id'];

        $sql = "
            SELECT *
            FROM artists
            WHERE idArtist = :id
        ";

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':id', $id);

        $stmt->execute();

        $artist = $stmt->fetch();

        if (!$artist) {

            $response->getBody()->write(
                json_encode([
                    'error' => 'Artiste introuvable'
                ])
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }

        $response->getBody()->write(
            json_encode($artist)
        );

        return $response->withHeader(
            'Content-Type',
            'application/json'
        );
    });


    // =====================================================
    // POST - AJOUTER UN ARTISTE
    // PROTEGE PAR TOKEN JWT
    // =====================================================

    $app->post('/addArtist', function (
        Request $request,
        Response $response
    ) {

        $db = $this->get(PDO::class);

        $data = $request->getParsedBody();

        $name = $data['Name'] ?? '';
        $annee = $data['Annee'] ?? '';
        $description = $data['Description'] ?? '';

        if (
            empty($name) ||
            empty($annee) ||
            empty($description)
        ) {

            $response->getBody()->write(
                json_encode([
                    'error' => 'Informations manquantes'
                ])
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        $sql = "
            INSERT INTO artists
            (Name, Annee, Description)
            VALUES
            (:name, :annee, :description)
        ";

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':annee', $annee);
        $stmt->bindParam(':description', $description);

        $stmt->execute();

        $insertedID = $db->lastInsertId();

        $response->getBody()->write(
            json_encode([
                'message' => 'Artiste ajouté avec succès',
                'idArtist' => $insertedID
            ])
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);

    })->add(new JwtMiddleware());


    // =====================================================
    // GET - ROUTE PROTEGEE JWT
    // =====================================================

    $app->get('/protected', function (
        Request $request,
        Response $response
    ) {

        $response->getBody()->write(
            json_encode([
                'message' => 'Hello, SaintMichel'
            ])
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    })->add(new JwtMiddleware());

};