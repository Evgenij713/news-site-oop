<?php
declare(strict_types=1);

namespace Modules\Articles\Controllers;

use Modules\_base\ArticleController;
use Modules\Articles\Models\Index as ModelsIndex;
use System\Template;

class Articles extends ArticleController
{
    public function __construct(array $parameters, array $user, string $pageCanonical)
    {
        parent::__construct($parameters, $user, $pageCanonical);
        $this->model = ModelsIndex::getInstance();

        $this->handleRequest();
    }

    protected function handleGetRequest(array $parameters = []): void
    {
        $articles = $this->model->sortAll(['date_add'], true);
        $this->prepareArticles($articles);

        $this->title = 'Список статей';
        $this->content = Template::render(__DIR__ . '/../Views/v_all.php', [
            'articles' => $articles
        ]);
    }

    // Реализация абстрактного метода (не используется в этом контроллере)
    protected function handlePostRequest(array $parameters = []): void {}
}