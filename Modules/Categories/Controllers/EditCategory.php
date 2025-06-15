<?php
declare(strict_types=1);

namespace Modules\Categories\Controllers;

use Modules\_base\CategoryController;
use System\Template;

class EditCategory extends CategoryController
{
    public function __construct(array $parameters, array $user, string $pageCanonical)
    {
        parent::__construct($parameters, $user, $pageCanonical);
        $this->strId = $parameters['id'] ?? '';
        $this->loadCategory((int)$this->strId);
        $this->redirectIfNoPermission();

        $this->handleRequest($parameters);
    }

    protected function handleGetRequest(array $parameters = []): void
    {
        $this->title = 'Редактирование категории';
        $this->prepareCategory($this->category);

        $this->content = Template::render(__DIR__ . '/../Views/v_edit.php', [
            'category' => $this->category
        ]);
    }

    protected function handlePostRequest(array $parameters = []): void
    {
        $response = ['res' => false, 'err' => [], 'currentUrl' => BASE_URL];

        if ($this->model->idCheck($this->strId)) {
            $fields = $this->model->extractFields($_POST, ['name']);
            $response['err'] = $this->validateCategoryFields($fields);

            if (empty($response['err'])) {
                $response['res'] = $this->model->edit((int)$this->strId, $fields);
                if (!$response['res']) {
                    $response['err'][] = 'Ошибка при редактировании';
                }
            }
        }

        $this->jsonResponse($response);
    }
}