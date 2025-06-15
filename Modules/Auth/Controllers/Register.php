<?php
declare(strict_types=1);

namespace Modules\Auth\Controllers;

use Modules\_base\AuthController;
use Modules\Auth\Models\Index as ModelsIndex;
use Random\RandomException;
use System\Template;

class Register extends AuthController
{
    public function __construct(array $parameters, array $user, string $pageCanonical) {
        parent::__construct($parameters, $user, $pageCanonical);
        $this->model = ModelsIndex::getInstance();

        $this->redirectIfAuthenticated();

        $this->handleRequest();
    }

    protected function handleGetRequest(array $parameters = []): void {
        $this->title = 'Регистрация';
        $this->content = Template::render(__DIR__ . '/../Views/v_register.php');
    }

    /**
     * @throws RandomException
     */
    protected function handlePostRequest(array $parameters = []): void {
        $response = ['res' => false, 'err' => [], 'currentUrl' => BASE_URL];

        $fields = $this->modelUsers->extractFields($_POST, ['name', 'email', 'login', 'password', 'repeat-password']);

        $response['err'] = $this->modelUsers->isValid($fields);

        if ((bool)$this->modelUsers->usersOne($fields['login'])) {
            $response['err'][] = 'Такой логин уже зарегистрирован.';
        }

        if (empty($response['err'])) {
            $fields['password'] = password_hash($fields['password'], PASSWORD_BCRYPT);
            unset($fields['repeat-password']);
            $this->modelUsers->createUser($fields);

            $user = $this->modelUsers->usersOne($fields['login']);
            $this->model->authCreateSession((int)$user['id_user']);

            $response['res'] = true;
        }

        $this->jsonResponse($response);
    }
}