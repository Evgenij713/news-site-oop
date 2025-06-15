<?php
declare(strict_types=1);

namespace Modules\Auth\Models;

use System\Model;

class Users extends Model{
	protected static $instance;
	protected string $table = 'users';
	protected string $pk = 'id_user';

    protected array $validationRules = [
        'name' => [
            'min_length' => '3',
            'max_length' => 128,
            'message' => 'Имя должно быть от 3 до 128 символов.'
        ],
        'login' => [
            'min_length' => 6,
            'max_length' => 50,
            'message' => 'Логин должен быть от 6 до 50 символов.'
        ],
        'password' => [
            'min_length' => 6,
            'max_length' => 60,
            'message' => 'Пароль должен быть от 6 до 60 символов.'
        ],
        'email' => [
            'min_length' => 6,
            'max_length' => 256,
            'message' => 'Email должен быть от 6 до 256 символов.'
        ]
    ];

    public function usersOne(string $login) : array {
        $res = $this->select(['login' => $login]);
        return $res[0] ?? [];
    }

    public function createUser(array $fields) : int {
        return $this->add($fields);
    }

    public function usersById(int $id): array {
        return $this->get($id);
    }
}