<?php
declare(strict_types=1);

namespace Modules\Articles\Controllers;

use Modules\_base\ArticleController;
use System\Template;

class CategoryArticles extends ArticleController
{
    public function __construct(array $parameters, array $user, string $pageCanonical) {
        parent::__construct($parameters, $user, $pageCanonical);

        $this->handleRequest($parameters);
    }

    protected function handleGetRequest(array $parameters = []): void
    {
        $this->strId = $parameters['id'] ?? '';
        $articles = $this->model->select(['category' => (int)$this->strId], ['date_add'], true);

        if (empty($articles)) {
            $this->redirectTo404();
        }

        $this->title = 'Новости категории';
        $category = $this->getCategory((int)$this->strId);

        $this->prepareArticles($articles);

        $this->content = Template::render(__DIR__ . '/../Views/v_category.php', [
            'category' => $category,
            'articles' => $articles
        ]);
    }

    // Реализация абстрактного метода (не используется)
    protected function handlePostRequest(array $parameters = []): void {}
}