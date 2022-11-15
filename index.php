<?php require_once __DIR__ . '/vendor/autoload.php';

use App\DB;
use App\QueryBuilder;
use App\User;

$pdo = new DB();
$pdo = $pdo->getPDO();
if (is_string($pdo)) {
    dump($pdo);
    exit();
}
// $builder = new QueryBuilder($pdo, 'insert');
// $pdo->query("CREATE TABLE users(id int PRIMARY KEY, pseudo VARCHAR(50) NOT NULL, password VARCHAR(50) NOT NULL)");

// for ($i = 1; $i <= 10; $i++) {
//     $builder->from('users')
//         ->fields('id', "pseudo", 'password')
//         ->setParams(['id' => $i, 'pseudo' => "pseudo {$i}", 'password' => "Password {$i}"])
//         ->persist();
//     // $query = $pdo->prepare("INSERT INTO users SET id = :id, pseudo = :pseudo, password = :password");
//     // $query->execute(['id' => $i, 'pseudo' => "pseudo{$i}", 'password' => "password{$i}"]);
//     // $pdo->query("INSERT INTO users SET id = '" . $i ."', pseudo = '" . 'pseudo' .$i . "', password = '" . 'password' .$i . "'");
// }

// $pdo = new DB('mysql', 'sadsd', user: 'root', password: 'root');
$builder = (new QueryBuilder($pdo, 'update'))->from('users')->where('id = ?');
dump($builder);
dump($builder->reset()->from('posts')->setParams(['id' => 1])->fields('category', 'tags'));