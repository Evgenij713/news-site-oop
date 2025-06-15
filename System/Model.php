<?php
declare(strict_types=1);

namespace System;

use System\Database\Connection;
use System\Database\QuerySelect;
use System\Database\SelectBuilder;

abstract class Model{
	protected static $instance;
	protected Connection $db;
	protected string $table;
	protected string $pk;
	protected array $validationRules = [];
	protected Validator $validator;

	public static function getInstance() : static{
		if(static::$instance === null){
			static::$instance = new static();
		}

		return static::$instance;
	}

	protected function __construct(){
		$this->db = Connection::getInstance();
		$this->validator = new Validator($this->validationRules);
	}

	public function all() : array{
		return $this->selector()->get();
	}

    public function sortAll(array $fields, bool $desc = false) : array{
        return $this->selector()->groupBy($fields, $desc)->get();
    }

	public function get(int $id) : array{
		$res = $this->selector()->where("{$this->pk} = :pk", ['pk' => $id])->get();
		return $res[0];
	}

    public function select(array $fields,  array $sortFields = [], bool $desc = false): array{
        $masks = [];

        foreach($fields as $field => $val){
            $masks[] = "$field = :$field";
        }

        $masksStr = implode(' AND ', $masks);

        return $this->selector()->where($masksStr, $fields)->groupBy($sortFields, $desc)->get();
    }

	public function selector() : QuerySelect{
		$builder = new SelectBuilder($this->table);
		return new QuerySelect($this->db, $builder);
	}

    public function isValid(array $fields): array {
        return $this->validator->run($fields);
    }

    public function add(array $fields): int{
		$names = [];
		$masks = [];

		foreach($fields as $field => $val){
			$names[] = $field;
			$masks[] = ":$field";
		}

		$namesStr = implode(', ', $names);
		$masksStr = implode(', ', $masks);

		$query = "INSERT INTO {$this->table} ($namesStr) VALUES ($masksStr)";
		$this->db->query($query, $fields);
		return $this->db->lastInsertId();
	}

    public function edit(int $id, array $fields): bool{
        $masks = [];

        foreach($fields as $field => $val){
            $masks[] = $field . " = :$field";
        }

        $masksStr = implode(', ', $masks);

        $query = "UPDATE {$this->table} SET {$masksStr} WHERE {$this->pk} = :{$this->pk}";

        $this->db->query($query, $fields + [$this->pk => $id]);
        return true;
    }

    public function remove(int $id): bool {
        $query = "DELETE FROM {$this->table} WHERE {$this->pk} = :id";

        $stmPdo = $this->db->query($query, ['id' => $id]);
        return $stmPdo->rowCount() > 0;
    }

    public function cropText(string $str): string {
        if (strlen($str) > 99) {
            return mb_substr($str, 0, 99) . '...';
        }
        else {
            return $str;
        }
    }

    public function idCheck(string $strId): bool {
        $pattern = '/^[1-9]\d*$/';
        return !!preg_match($pattern, $strId);
    }

    public function extractFields(array $target, array $fields): array {
        $result = [];

        foreach ($fields as $field) {
            if (isset($target[$field])) {
                $result[$field] = trim($target[$field]);
            }
            else {
                $result[$field] = '';
            }
        }

        return $result;
    }
}