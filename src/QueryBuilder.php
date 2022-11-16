<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;
use PDOStatement;

class QueryBuilder
{

    private string $statement;

    private string $from;

    private array $fields;

    private ?string $where;

    private ?string $innerJoin;

    private ?string $leftJoin;

    private ?string $rightJoin;

    private ?array $params;

    private ?string $groupBy;

    private ?string $having;

    private ?string $orderBy;

    private ?int $limit;

    private ?int $offset;

    private PDO $pdo;

    private ?PDOStatement $preparedStatement = null;

    public function __construct(PDO $pdo, string $query = 'SELECT')
    {
        $this->pdo = $pdo;
        $this->init($query);
    }

    public function from(string ...$table): self
    {
        $this->from = implode(", ", $table);
        return $this;
    }

    public function fields(string ...$fields): self
    {
        $this->fields = $fields;
        return $this;
    }

    public function innerJoin(string ...$innerJoin): self
    {
        $this->innerJoin = str_replace('on', 'ON', implode(' INNER JOIN ', $innerJoin));
        return $this;
    }

    public function leftJoin(string ...$leftJoin): self
    {
        $this->leftJoin = str_replace('on', 'ON', implode(' LEFT JOIN ', $leftJoin));
        return $this;
    }

    public function rightJoin(string ...$rightJoin): self
    {
        $this->rightJoin = str_replace('on', 'ON', implode(' RIGHT JOIN ', $rightJoin));
        return $this;
    }

    public function where(string ...$where): self
    {
        $this->where = implode(' AND ', $where);
        return $this;
    }

    public function setParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    public function groupBy(string ...$groupBy): self
    {
        $this->groupBy = implode(', ', $groupBy);
        return $this;
    }

    public function having(string ...$having): self
    {
        $this->having = implode(' AND ', $having);
        return $this;
    }

    public function orderBy(string ...$orderBy): self
    {
        $this->orderBy = implode(", ", $orderBy);
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function get()
    {
        $this->executeQuery();
        $statement = $this->preparedStatement;
        $this->reset();
        return $statement->fetch();
    }

    public function persist()
    {
        $executed = $this->executeQuery();
        $this->reset();
        return $executed;

    }

    /**
     * Prepare la requete en relevant les differentes erreurs potentielles
     * @return void|self
     */
    private function prepareQuery()
    {
        try {
            $this->toSQL();
            $this->preparedStatement = $this->pdo->prepare($this->statement);
            return $this;
        } catch (PDOException $e) {
            throw new PDOException("Une erreur est survenue lors du traitement de la requête SQL : {$e->getMessage()}");
        }
    }

    private function executeQuery()
    {
        $this->prepareQuery();
        return $this->preparedStatement->execute($this->params);
    }

    /**
     * Comme son nom l'indique, elle permet de retourner une chaine de caractere a partir du builder
     * @return string
     */
    public function toSQL(): string
    {
        $query = $this->statement;
        $implodedFields = implode(', ', $this->fields);
        switch ($query) {
            case 'INSERT':
                $insertValues = array_map(function ($f) {
                    return ":{$f}";
                }, $this->fields);
                $query .= " INTO {$this->from}({$implodedFields}) VALUES(" . implode(', ', $insertValues) . ")";
                break;
            case 'SELECT':
                $query .= " {$implodedFields} FROM {$this->from}";
                break;
            case 'DELETE':
                $query .= " FROM {$this->from}";
                break;
            case 'UPDATE':
                $query .= " FROM {$this->from} SET {$implodedFields}";
                break;
            default:
                break;
        }
        unset($implodedFields);

        if ($this->innerJoin) {
            $query .= " INNER JOIN {$this->innerJoin}";
        }

        if ($this->leftJoin) {
            $query .= " LEFT JOIN {$this->leftJoin}";
        }

        if ($this->rightJoin) {
            $query .= " RIGHT JOIN {$this->rightJoin}";
        }

        if ($this->where) {
            $query .= " WHERE {$this->where}";
        }

        if ($this->groupBy) {
            $query .= " GROUP BY {$this->groupBy}";
        }

        if ($this->having) {
            $query .= " HAVING {$this->having}";
        }

        if ($this->orderBy) {
            $query .= " ORDER BY {$this->orderBy}";
        }

        if ($this->limit !== 0) {
            $query .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== 0) {
            $query .= " OFFSET {$this->offset}";
        }

        $this->statement = $query;
        return $this->statement;
    }

    public function getAll(): array
    {
        $this->executeQuery();
        $statement = $this->preparedStatement;
        $this->reset();
        return $statement->fetchAll();

    }

    /**
     * Reinitialise le Query Builder
     *
     * @param string|null $query
     * @return self
     */
    public function reset(?string $query = 'SELECT'): self
    {
        $this->init($query);
        return $this;
    }

    private function init(?string $query = 'SELECT') {
        $this->statement = strtoupper($query);
        $this->where = '';
        $this->innerJoin = '';
        $this->leftJoin = '';
        $this->rightJoin = '';
        $this->params = [];
        $this->fields = ['*'];
        $this->groupBy = '';
        $this->having = '';
        $this->orderBy = '';
        $this->limit = 0;
        $this->offset = 0;
        unset($this->from, $this->preparedStatement);
    }
}
