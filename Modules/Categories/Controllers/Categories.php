<?php
declare(strict_types=1);

namespace Modules\Categories\Controllers;

use Modules\_base\CategoryController;
use System\Template;

class Categories extends CategoryController {
    public function __construct(array $parameters, array $user, string $pageCanonical) {
        parent::__construct($parameters, $user, $pageCanonical);
        $this->redirectIfNoPermission();
        $this->handleRequest();
    }

    protected function handleGetRequest(array $parameters = []): void {
        $this->title = 'Редактор категорий';
        $categories = $this->model->sortAll(['id_category'], true);

        foreach ($categories as $key => $category) {
            $this->prepareCategory($categories[$key]);
        }

        $this->content = Template::render(__DIR__ . '/../Views/v_all.php', [
            'categories' => $categories
        ]);
    }

    protected function handlePostRequest(array $parameters = []): void {
        $response = ['res' => false, 'err' => [], 'currentUrl' => ''];

        $this->strId = $_POST['id'] ?? '';

        if ($this->model->idCheck($this->strId)) {
            if ($this->model->isCategoryEmpty((int)$this->strId)) {
                $response['res'] = $this->model->remove((int)$this->strId);
                if (!$response['res']) {
                    $response['err'][] = 'Ошибка при удалении.';
                }
            } else {
                $response['err'][] = 'Невозможно удалить категорию, так как в ней находятся статьи.';
            }
        }

        $this->jsonResponse($response);
    }
}