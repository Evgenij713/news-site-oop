<?php
declare(strict_types=1);

namespace System\Database;
use System\Exceptions\ExcFatal;

class SelectBuilder{
	public string $table;
	protected array $fields = ['*'];
	protected array $addons = [
		'join' => '',
		'where' => '',
		'group_by' => '',
		'having' => '',
		'order_by' => '',
		'limit' => ''
	];

	public function __construct(string $table){
		$this->table = $table;
	}

	public function fields(array $fields): static{
		$this->fields = $fields;
		return $this;
	}

	public function addWhere(string $where): static{
		$this->addons['where'] .= ' ' . $where;
		return $this;
	}

    public function group(string $condition): static {
        $this->addons['group_by'] .= $condition;
        return $this;
    }

    public function limit(string $condition): static {
        $this->addons['limit'] .= $condition;
        return $this;
    }

	public function __toString(){
		$activeCommands = [];
		
		foreach($this->addons as $command => $setting){
			if($setting !== ''){
				$sqlKey = str_replace('_', ' ', strtoupper($command));
				$activeCommands[] = "$sqlKey $setting";
			}
		}

		$fields = implode(', ', $this->fields);
		$addon = implode(' ', $activeCommands);
		return trim("SELECT $fields FROM {$this->table} $addon");
	}

    /**
     * @throws ExcFatal
     */
    public function __call($name, $args){
		if(!array_key_exists($name, $this->addons)){
			throw new ExcFatal('sql error unknown');
		}

		$this->addons[$name] = $args[0];
		return $this;
	}
}