<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;

setlocale(LC_TIME, 'fr');

class DB
{
    private ?\PDO $pdo;

    public function __construct(string $dsn = 'sqlite', string $name = 'database.sqlite', string $host = 'localhost', string $user = null, string $password = null)
    {
        try {
            $server_string = $dsn . ':' . $name;
            $dsn !== 'sqlite' ? $server_string = $dsn . ':dbname=' . $name . ';host=' . $host : false;
            $this->pdo = new PDO($server_string, $user, $password, [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        } catch (PDOException $e) {
            echo "Une erreur est survenue lors de la connexion à la base données : {$e->getMessage()}";
            exit();
        }
    }

    public function getPDO(): PDO
    {
        return $this->pdo;
    }
}
