<?php
declare(strict_types=1);

namespace Modules\Categories\Models;

use System\Model;
use Modules\Articles\Models\Index as ModelsArticles;

class Index extends Model{
	protected static $instance;
	protected string $table = 'categories';
	protected string $pk = 'id_category';
    protected ModelsArticles $modelArticles;

    protected function __construct(){
        parent::__construct();
        $this->modelArticles = ModelsArticles::getInstance();
    }

    // Функция проверки доступа
    public function checkPermissions(int $role): bool {
        return $role === 1 || $role === 2;
    }

    // Функция проверки уникальности
    public function isCategoryUnique(string $value): bool {
        return !(bool)$this->select(['name' => $value]);
    }

    // Функция проверки наличия статей в категории
    public function isCategoryEmpty(int $idCategory): bool {
        return !(bool)$this->modelArticles->select(['category' => $idCategory]);
    }

	protected array $validationRules = [
		'name' => [
            'min_length' => '3',
            'max_length' => 128,
            'message' => 'Название категории должно быть от 3 до 128 символов.'
        ]
	];
}