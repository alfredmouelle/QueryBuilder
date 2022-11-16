<?php require_once __DIR__ . '/vendor/autoload.php';

use App\DB;
use App\QueryBuilder;

$pdo = new DB();
$pdo = $pdo->getPDO();
if (is_string($pdo)) {
    dump($pdo);
    exit();
}

$builder = new QueryBuilder($pdo);

function createUsersTable(QueryBuilder $builder) {
    $builder->query("CREATE TABLE users(
        id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, 
        username VARCHAR NOT NULL, 
        password VARCHAR NOT NULL)"
    )->persist();
}

function deleteUsersTable(QueryBuilder $builder) {
    return $builder->query('DROP TABLE users')->persist();
}

function createDumpUsers(QueryBuilder $builder): array
{
    for ($i = 1; $i <= 10; $i++) {
        $builder->reset('insert')->from('users')
            ->fields("username", 'password')
            ->setParams(['username' => "user {$i}", 'password' => "1234"])
            ->persist();
    
    }
    return $builder->from('users')->getAll();
}

function getAllUsers(QueryBuilder $builder) {
    return $builder->from('users')->getAll();
}

function deleteAllUsers(QueryBuilder $builder): bool
{
    return $builder->query('DROP TABLE users')->persist();
}


// createUsersTable($builder);
// createDumpUsers($builder);
// deleteUsersTable($builder);

dump(getAllUsers($builder));