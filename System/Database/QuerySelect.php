<?php
declare(strict_types=1);

namespace System\Database;

class QuerySelect{
	protected Connection $db;
	protected SelectBuilder $builder;
	protected array $binds = [];

	public function __construct(Connection $db, SelectBuilder $builder){
		$this->db = $db;
		$this->builder = $builder;
	}

	public function where(string $where, array $binds = []): static {
		$this->builder->addWhere($where);
		$this->binds = $binds + $this->binds;
		return $this;
	}

    public function groupBy(array $fields, bool $desc = false): static {
        $condition = implode(', ', $fields);
        if ($desc === true) {
            $condition .= ' DESC';
        }
        $this->builder->group($condition);
        return $this;
    }

	public function limit(int $shift, ?int $cnt = null): static {
		$this->builder->limit($shift . (($cnt !== null) ? ",$cnt" : ''));
		return $this;
	}

	public function get() : array{
		return $this->db->select((string)$this->builder, $this->binds);
	}

	/* join, order ... */

	/* public function __invoke(){
		return $this->db->select($this->builder, $this->binds);
	} */
}