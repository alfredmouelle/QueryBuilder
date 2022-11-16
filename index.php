<?php require_once __DIR__ . '/vendor/autoload.php';

use App\DB;
use App\QueryBuilder;

$pdo = new DB();
$pdo = $pdo->getPDO();
if (is_string($pdo)) {
    dump($pdo);
    exit();
}

// Si vous avez besoin de populer la base de données

/* $builder = new QueryBuilder($pdo, 'insert');
$pdo->query("CREATE TABLE users(
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, 
    username VARCHAR NOT NULL, 
    password VARCHAR NOT NULL)");

for ($i = 1; $i <= 10; $i++) {
    $builder->from('users')
        ->fields("username", 'password')
        ->setParams(['username' => "user {$i}", 'password' => "1234"])
        ->persist();

} */

// $pdo->query('DROP TABLE users');

$builder = new QueryBuilder($pdo, 'delete');
// $builder->from('users')->where('id = ?')->setParams([4])->persist();

$users = $builder->reset()->from('users')->getAll();
dump($users);