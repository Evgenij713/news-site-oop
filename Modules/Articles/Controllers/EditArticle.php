<?php
declare(strict_types=1);

namespace Modules\Articles\Controllers;

use Modules\_base\ArticleController;
use System\Template;

class EditArticle extends ArticleController
{
    public function __construct(array $parameters, array $user, string $pageCanonical) {
        parent::__construct($parameters, $user, $pageCanonical);
        $this->strId = $parameters['id'] ?? '';

        $this->loadArticle((int)$this->strId);
        $this->checkArticlePermissions();

        $this->handleRequest($parameters);
    }

    protected function handleGetRequest(array $parameters = []): void {
        $this->title = 'Редактирование статьи';

        $this->article['date'] = date('Y-m-d', strtotime($this->article['date_add']));
        $this->article['time'] = date('H:i:s', strtotime($this->article['date_add']));
        $this->prepareSingleArticles($this->article);

        $this->content = Template::render(__DIR__ . '/../Views/v_edit.php', [
            'article' => $this->article,
            'categories' => $this->getCategories()
        ]);
    }

    protected function handlePostRequest(array $parameters = []): void {
        $response = ['res' => false, 'err' => [], 'currentUrl' => BASE_URL];

        if ($this->model->idCheck($this->strId)) {
            $fields = $this->prepareArticleFields($_POST);
            $response['err'] = $this->validateArticleFields($fields);

            if (empty($response['err'])) {
                $response['res'] = $this->model->edit((int)$this->strId, $fields);
                if (!$response['res']) {
                    $response['err'][] = 'Ошибка при редактировании.';
                }
            }
        }

        $this->jsonResponse($response);
    }
}