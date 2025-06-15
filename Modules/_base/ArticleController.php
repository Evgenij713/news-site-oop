<?php
declare(strict_types=1);

namespace Modules\_base;

use Modules\Articles\Models\Index as ModelsArticles;
use Modules\Categories\Models\Index as ModelsCategories;

abstract class ArticleController extends BaseController
{
    protected ModelsCategories $modelCategories;
    protected array $article = [];
    protected int $idUser;

    public function __construct(array $parameters, array $user, string $pageCanonical) {
        parent::__construct($parameters, $user, $pageCanonical);
        $this->model = ModelsArticles::getInstance();
        $this->modelCategories = ModelsCategories::getInstance();
        $this->idUser = $user['id_user'] ?? 0;
    }

    protected function loadArticle(int $id): void
    {
        $this->article = $this->model->get($id);
        if (empty($this->article)) {
            $this->redirectTo404();
        }
    }

    protected function checkArticlePermissions(): void
    {
        if (!$this->model->checkPermissions($this->article['user'], $this->idUser, $this->role)) {
            $this->redirectTo403();
        }
    }

    protected function validateArticleFields(array $fields): array
    {
        $errors = $this->model->isValid($fields);

        // Дополнительные проверки для статей
        if (empty($fields['category'])) {
            $errors[] = 'Необходимо выбрать категорию.';
        }

        return $errors;
    }

    protected function prepareArticleFields(array $postData): array
    {
        $fields = $this->model->extractFields($postData, ['title', 'content', 'category']);

        $auxiliaryFields = $this->model->extractFields($postData, ['date', 'time']);
        $fields['date_add'] = $auxiliaryFields['date'] . ' ' . $auxiliaryFields['time'];
        $fields['user'] = $this->idUser;

        return $fields;
    }

    protected function prepareArticles(array &$articles): void
    {
        foreach ($articles as $key => $article) {
            $articles[$key]['title'] = htmlspecialchars($this->model->cropText($article['title']));
            $articles[$key]['content'] = htmlspecialchars($this->model->cropText($article['content'])) . '...';
        }
    }

    protected function prepareSingleArticles(array &$article): void
    {
        $article['title'] = htmlspecialchars($article['title']);
        $article['content'] = htmlspecialchars($article['content']);
    }

    protected function getCategories(): array
    {
        $categories = $this->modelCategories->all();
        foreach ($categories as $key => $category) {
            $categories[$key]['name'] = htmlspecialchars($category['name']);
        }
        return $categories;
    }

    protected function getCategory(int $idCategory): array
    {
        $category = $this->modelCategories->get($idCategory);
        $category['name'] = htmlspecialchars($category['name']);

        return $category;
    }
}