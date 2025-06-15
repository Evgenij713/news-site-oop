<?php
declare(strict_types=1);

namespace Modules\Articles\Models;

use System\Model;

class Index extends Model{
	protected static $instance;
	protected string $table = 'articles';
	protected string $pk = 'id_article';

    // Функция проверки доступа
    public function checkPermissions(int $userArticle, int $idUser, int $role): bool {
        return $userArticle === $idUser || $role === 1 || $role === 2;
    }

	protected array $validationRules = [
		'title' => [
            'min_length' => '3',
            'max_length' => 256,
            'message' => 'Заголовок должен быть от 3 до 256 символов.'
        ],
		'content' => [
            'min_length' => 10,
            'max_length' => 5000,
            'message' => 'Текст должен быть от 10 до 5000 символов.'
        ]
	];
}