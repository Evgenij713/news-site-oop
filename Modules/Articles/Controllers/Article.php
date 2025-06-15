<?php
declare(strict_types=1);

namespace Modules\Articles\Controllers;

use Modules\_base\ArticleController;
use System\Template;

class Article extends ArticleController
{
    public function __construct(array $parameters, array $user, string $pageCanonical) {
        parent::__construct($parameters, $user, $pageCanonical);
        $this->strId = $parameters['id'] ?? '';
        $this->loadArticle((int)$this->strId);

        $this->handleRequest($parameters);
    }

    protected function handleGetRequest(array $parameters = []): void {
        $this->title = 'Просмотр статьи';

        $this->prepareSingleArticles($this->article);

        $category = $this->getCategory($this->article['category']);

        $this->content = Template::render(__DIR__ . '/../Views/v_article.php', [
            'article' => $this->article,
            'category' => $category,
            'id_user' => $this->idUser,
            'role' => $this->role
        ]);
    }

    protected function handlePostRequest(array $parameters = []): void {
        $response = ['res' => false, 'err' => [], 'currentUrl' => BASE_URL];

        $this->checkArticlePermissions();

        if ($this->model->idCheck($this->strId)) {
            if ($this->model->remove((int)$this->strId)) {
                $response['res'] = true;
            } else {
                $response['err'][] = 'Ошибка при удалении.';
            }
        }

        $this->jsonResponse($response);
    }
}