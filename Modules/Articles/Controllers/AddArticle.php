<?php
declare(strict_types=1);

namespace Modules\Articles\Controllers;

use Modules\_base\ArticleController;
use System\Template;

class AddArticle extends ArticleController
{
    public function __construct(array $parameters, array $user, string $pageCanonical) {
        parent::__construct($parameters, $user, $pageCanonical);

        if (empty($this->user)) {
            $this->redirectTo403();
        }

        $this->handleRequest();
    }

    protected function handleGetRequest(array $parameters = []): void {
        $this->title = 'Добавление статьи';
        $this->content = Template::render(__DIR__ . '/../Views/v_add.php', [
            'categories' => $this->getCategories()
        ]);
    }

    protected function handlePostRequest(array $parameters = []): void {
        $response = ['res' => false, 'err' => [], 'lastInsertId' => ''];

        $fields = $this->prepareArticleFields($_POST);
        $response['err'] = $this->validateArticleFields($fields);

        if (empty($response['err'])) {
            $response['lastInsertId'] = $this->model->add($fields);
            $response['res'] = true;
        }

        $this->jsonResponse($response);
    }
}