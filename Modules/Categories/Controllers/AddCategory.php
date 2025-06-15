<?php
declare(strict_types=1);

namespace Modules\Categories\Controllers;

use Modules\_base\CategoryController;
use System\Template;

class AddCategory extends CategoryController {
    public function __construct(array $parameters, array $user, string $pageCanonical) {
        parent::__construct($parameters, $user, $pageCanonical);
        $this->redirectIfNoPermission();
        $this->handleRequest();
    }

    protected function handleGetRequest(array $parameters = []): void {
        $this->title = 'Добавление категории';
        $this->content = Template::render(__DIR__ . '/../Views/v_add.php');
    }

    protected function handlePostRequest(array $parameters = []): void {
        $response = ['res' => false, 'err' => [], 'lastInsertId' => ''];

        $fields = $this->model->extractFields($_POST, ['name']);
        $response['err'] = $this->validateCategoryFields($fields);

        if (empty($response['err'])) {
            $response['lastInsertId'] = $this->model->add($fields);
            $response['res'] = true;
        }

        $this->jsonResponse($response);
    }
}