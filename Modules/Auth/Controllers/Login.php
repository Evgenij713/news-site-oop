<?php
declare(strict_types=1);

namespace Modules\Auth\Controllers;

use Modules\_base\AuthController;
use Modules\Auth\Models\Index as ModelsIndex;
use Random\RandomException;
use System\Template;

class Login extends AuthController {
    public function __construct(array $parameters, array $user, string $pageCanonical) {
        parent::__construct($parameters, $user, $pageCanonical);
        $this->model = ModelsIndex::getInstance();

        $this->redirectIfAuthenticated();

        $this->handleRequest();
    }

    protected function handleGetRequest(array $parameters = []): void {
        $this->title = 'Страница авторизации';
        $this->content = Template::render(__DIR__ . '/../Views/v_login.php');
    }

    /**
     * @throws RandomException
     */
    protected function handlePostRequest(array $parameters = []): void {
        $response = ['res' => false, 'err' => [], 'currentUrl' => BASE_URL];

        $fields = $this->model->extractFields($_POST, ['login', 'password']);
        $remember = isset($_POST['remember-me']);

        $response['err'] = $this->modelUsers->isValid($fields);

        if (empty($response['err'])) {
            $user = $this->modelUsers->usersOne($fields['login']);
            if (!empty($user) && password_verify($fields['password'], $user['password'] ?? '')) {
                $token = $this->model->authCreateSession((int)$user['id_user']);
                if ($remember) {
                    setcookie('token', $token, time() + 3600 * 24 * 30, BASE_URL);
                }
                $response['res'] = true;
            } else {
                $response['err'][] = 'Неверный логин или пароль.';
            }
        }

        $this->jsonResponse($response);
    }
}