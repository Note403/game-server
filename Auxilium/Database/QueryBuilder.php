<?php

declare(strict_types = 1);

namespace Auxilium\Database;

use Exception;
use Auxilium\Support\ArrayHelper as Arr;
use Auxilium\Support\Config;

class QueryBuilder
{
    private string $query;
    private array $where_clauses;
    private string $table;
    private array $fillable;
    private int $limit;
    private array $operators = ['=', '>=', '<=', '!='];

    private string $dbName;
    private DB $db;

    /**
     * @param string $table
     * @param array $columns
     * @throws Exception
     */
    public function __construct(string $table, array $columns)
    {
        $this->table = $table;
        $this->fillable = $columns;
        $this->dbName = Config::dbData()['database'];

        if ($this->dbName == null)
            throw new Exception('NO DB-NAME');

        $this->db = new DB();

        if (!$this->db->open())
            throw new Exception('DB CONNECTION ERROR');
    }

    /**
     * @param string $column
     * @param string $operator
     * @param string|null $value
     * @return self
     * @throws Exception
     */
    public function where(string $column, string $operator, string $value = null): self
    {
        if ($this->isOperator($operator) && $value == null)
            throw new Exception('ERROR');

        if (!empty($this->where_clauses)) {
            $clause = " AND";
        } else {
            $clause = '';
        }

        if (!$this->isColumn($column))
            throw new Exception('ERROR');

        if ($this->isOperator($operator)) {
            $this->where_clauses[] = $clause . " WHERE " . $this->addColumn($column) . " " . $operator . " " . $this->addValue($value);
        } else {
            $this->where_clauses[] = $clause . " WHERE " . $this->addColumn($column) . " = " . $this->addValue($operator);
        }

        return $this;
    }

    /**
     * @param string $column
     * @param string $operator
     * @param string|null $value
     * @return QueryBuilder
     * @throws Exception
     */
    public function orWhere(string $column, string $operator, string $value = null): self
    {
        if ($this->isOperator($operator) && $value == null)
            throw new Exception('ERROR');

        if (empty($this->where_clauses))
            throw new Exception('ERROR');

        if (!$this->isColumn($column))
            throw new Exception('ERROR');

        if ($this->isOperator($operator)) {
            $this->where_clauses[] = " OR WHERE " . $this->addColumn($column) . " " . $operator . " " . $this->addvalue($value);
        } else {
            $this->where_clauses[] = " OR WHERE " . $this->addValue($column) . " = " . $this->addValue($operator);
        }

        return $this;
    }

    /**
     * @param array $values
     * @return array
     * @throws Exception
     */
    public function update(array $values): array
    {
        if (empty($values)) {
            throw new Exception('ERROR');
        }

        $this->query = "UPDATE " . $this->addTable() . " SET ";

        foreach ($values as $column => $value) {
            if (!$this->isColumn($column))
                throw new Exception('ERROR');

            $this->query .= $column . " = " . $this->addValue($value) . ($column != array_key_last($values)) ? ', ' : null;
        }

        return $this->build();
    }

    /**
     * @param array $values
     * @return bool
     * @throws Exception
     */
    public function create(array $values): bool
    {
        $this->query = "INSERT INTO " . $this->addTable() . " ";

        $queryColumns = '(';
        $queryValues = '(';

        foreach ($values as $column => $value) {
            if (!$this->isColumn($column))
                throw new Exception("Column: {$column} does not exist in {$this->table}");

            if ($queryColumns == '(' && $queryValues == '(') {
                $queryColumns .= $column;
                $queryValues .= "'{$value}'";
            } else {
                $queryColumns .= ", {$column}";
                $queryValues .= ", '{$value}'";
            }
        }

        $this->query .= " {$queryColumns}) VALUES {$queryValues})";

        $buildResponse = $this->build();

        if (!$buildResponse)
            throw new Exception("Query ERROR");

        return $buildResponse;
    }

    /**
     * @param array<string>|null $columns
     * @return array
     * @throws Exception
     */
    public function get(array $columns = null)
    {
        $this->query = "SELECT ";

        if ($columns == null) {
            $col = "*";
        } else {
            if (count($columns) == 1) {
                $col = Arr::first($columns);
            } else {
                $col = '';

                foreach ($columns as $idx => $column) {
                    if ($idx != (count($columns) - 1)) {
                        $col .= $this->addColumn($column) . ', ';
                    } else {
                        $col .= $this->addColumn($column);
                    }
                }
            }
        }

        $this->query .= $col . " FROM " . $this->addTable() . " ";

        return $this->build();
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function delete(): bool
    {
        $this->query = 'DELETE FROM ' . $this->table;

        if ($this->limit != null)
            $this->query .= 'LIMIT ' . $this->limit;

        return !is_array($this->build())
            ? throw new Exception('ERROR')
            : true;
    }

    /**
     * @return array|bool|\mysqli_result
     * @throws Exception
     */
    public function all()
    {
        $this->query = "SELECT * FROM " . $this->addTable();

        return $this->db->execute($this->query);
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit): self
    {
        if ($limit < 0)
            return $this;

        $this->limit = $limit;

        return $this;
    }

    /**
     * @return array|bool|\mysqli_result
     * @throws Exception
     */
    private function build()
    {
        if (!empty($this->where_clauses)) {
            foreach ($this->where_clauses as $where_clause) {
                $this->query .= $where_clause;
            }
        }

        return $this->db->execute($this->query);
    }

    /**
     * @param string $operator
     * @return bool
     */
    private function isOperator(string $operator): bool
    {
        return in_array($operator, $this->operators);
    }

    /**
     * @param string $value
     * @return string
     */
    private function escape(string $value): string
    {
        return $this->db->escape($value);
    }

    /**
     * @param string $column
     * @return bool
     */
    private function isColumn(string $column): bool
    {
        return in_array($column, $this->fillable);
    }

    /**
     * @param string $column
     * @return string
     */
    private function addColumn(string $column): string
    {
        return $this->table . "." . $this->escape($column);
    }

    /**
     * @param string $value
     * @return string
     */
    private function addValue(string $value): string
    {
        return "'" . $this->escape($value) . "'";
    }

    /**
     * @return string
     */
    private function addTable(): string
    {
        return $this->dbName . "." . $this->table;
    }
}