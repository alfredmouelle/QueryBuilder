<?php require_once __DIR__ . '/../vendor/autoload.php';

use App\DB;
use App\QueryBuilder;
use PHPUnit\Framework\TestCase;

final class QueryBuilderTest extends TestCase
{

    private function getBuilder(string $query = 'SELECT'): QueryBuilder
    {
        return new QueryBuilder((new DB())->getPDO(), $query);
    }

    public function testFrom() {
        $query = $this->getBuilder()->from("users")->toSQL();
        $this->assertEquals("SELECT * FROM users", $query);
    }

    public function testFromWithAlias() {
        $query = $this->getBuilder()->from("users u")->toSQL();
        $this->assertEquals("SELECT * FROM users u", $query);
    }

    public function testFromArray() {
        $query = $this->getBuilder()->from("users u", "posts p")->toSQL();
        $this->assertEquals("SELECT * FROM users u, posts p", $query);
    }

    public function testFields() {
        $query = $this->getBuilder()->from('users')->fields("id", "pseudo", "password")->toSQL();
        $this->assertEquals("SELECT id, pseudo, password FROM users", $query);
    }

    public function testWhere() {
        $query = $this->getBuilder()->from('users')->where('id > 1')->setParams([1])->toSQL();
        $this->assertEquals("SELECT * FROM users WHERE id > 1", $query);
    }

    public function testMultipleWhere() {
        $query = $this->getBuilder()->from('users')->where('id > 1', 'id != 0')->setParams([1])->toSQL();
        $this->assertEquals("SELECT * FROM users WHERE id > 1 AND id != 0", $query);
    }

    public function testOrderBy() {
        $query = $this->getBuilder()->from('users')->orderBy('pseudo')->toSQL();
        $this->assertEquals("SELECT * FROM users ORDER BY pseudo", $query);
    }

    public function testOrderByWithDirection() {
        $query = $this->getBuilder()->from('users')->orderBy('pseudo ASC')->toSQL();
        $this->assertEquals("SELECT * FROM users ORDER BY pseudo ASC", $query);
    }

    public function testMultiplesOrderBy() {
        $query = $this->getBuilder()->from('users')->orderBy('pseudo', 'id DESC')->toSQL();
        $this->assertEquals("SELECT * FROM users ORDER BY pseudo, id DESC", $query);
    }

    public function testGroupBy() {
        $query = $this->getBuilder()->from('users')->groupBy('pseudo')->toSQL();
        $this->assertEquals("SELECT * FROM users GROUP BY pseudo", $query);
    }

    public function testMultipleGroupBy() {
        $query = $this->getBuilder()->from('users')->groupBy('pseudo', 'id')->toSQL();
        $this->assertEquals("SELECT * FROM users GROUP BY pseudo, id", $query);
    }

    public function testHaving() {
        $query = $this->getBuilder()->from('users')->groupBy('pseudo', 'id')->having('id > 10')->toSQL();
        $this->assertEquals("SELECT * FROM users GROUP BY pseudo, id HAVING id > 10", $query);
    }

    public function testMultipleHaving() {
        $query = $this->getBuilder()->from('users')->groupBy('pseudo', 'id')->having('id > 10', 'pseudo = Kali')->toSQL();
        $this->assertEquals("SELECT * FROM users GROUP BY pseudo, id HAVING id > 10 AND pseudo = Kali", $query);
    }

    public function testlimit() {
        $query = $this->getBuilder()->from('users')->limit(5)->toSQL();
        $this->assertEquals("SELECT * FROM users LIMIT 5", $query);
    }

    public function testOffset() {
        $query = $this->getBuilder()->from('users')->offset(5)->toSQL();
        $this->assertEquals("SELECT * FROM users OFFSET 5", $query);
    }

    public function testDelete() {
        $query = $this->getBuilder('delete')->from('users')->toSQL();
        $this->assertEquals("DELETE FROM users", $query);
    }

    public function testDeleteWithCondition() {
        $query = $this->getBuilder('delete')->from('users')->where('id > 10')->toSQL();
        $this->assertEquals("DELETE FROM users WHERE id > 10", $query);
    }

    public function testUpdate() {
        $query = $this->getBuilder('update')->from('users')->fields('pseudo = :pseudo', 'password = :password')->where('id = 1')->toSQL();
        $this->assertEquals("UPDATE FROM users SET pseudo = :pseudo, password = :password WHERE id = 1", $query);
    }

    public function testInsert() {
        $query = $this->getBuilder('insert')->from('users')->fields('id', 'pseudo', 'password')->toSQL();
        $this->assertEquals("INSERT INTO users(id, pseudo, password) VALUES(:id, :pseudo, :password)", $query);
    }

    public function testInnerJoin() {
        $query = $this->getBuilder()->from('users u')->innerJoin('posts p ON p.author_id = u.id')->toSQL();
        $this->assertEquals("SELECT * FROM users u INNER JOIN posts p ON p.author_id = u.id", $query);
    }

    public function testLeftJoin() {
        $query = $this->getBuilder()->from('users u')->leftJoin('posts p ON p.author_id = u.id')->toSQL();
        $this->assertEquals("SELECT * FROM users u LEFT JOIN posts p ON p.author_id = u.id", $query);
    }

    public function testRightJoin() {
        $query = $this->getBuilder()->from('users u')->rightJoin('posts p ON p.author_id = u.id')->toSQL();
        $this->assertEquals("SELECT * FROM users u RIGHT JOIN posts p ON p.author_id = u.id", $query);
    }

    public function testMultipleInnerJoin() {
        $query = $this->getBuilder()->from('users u')
            ->innerJoin(
                'posts p on p.author_id = u.id',
                'carts c on c.owner = u.id'
            )->toSQL();
        $this->assertEquals("SELECT * FROM users u INNER JOIN posts p ON p.author_id = u.id INNER JOIN carts c ON c.owner = u.id", $query);
    }

    public function testMultipleLeftJoin() {
        $query = $this->getBuilder()->from('users u')
            ->leftJoin(
                'posts p on p.author_id = u.id',
                'carts c on c.owner = u.id'
            )->toSQL();
        $this->assertEquals("SELECT * FROM users u LEFT JOIN posts p ON p.author_id = u.id LEFT JOIN carts c ON c.owner = u.id", $query);
    }

    public function testMultipleRightJoin() {
        $query = $this->getBuilder()->from('users u')
            ->rightJoin(
                'posts p on p.author_id = u.id',
                'carts c on c.owner = u.id'
            )->toSQL();
        $this->assertEquals("SELECT * FROM users u RIGHT JOIN posts p ON p.author_id = u.id RIGHT JOIN carts c ON c.owner = u.id", $query);
    }
}
