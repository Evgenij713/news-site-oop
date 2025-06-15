<?php
declare(strict_types=1);

namespace Modules\Categories\Controllers;

use Modules\_base\CategoryController;
use System\Template;

class Category extends CategoryController
{
    public function __construct(array $parameters, array $user, string $pageCanonical)
    {
        parent::__construct($parameters, $user, $pageCanonical);
        // Проверка прав доступа
        $this->redirectIfNoPermission();

        $this->handleRequest($parameters);
    }

    protected function handleGetRequest(array $parameters = []): void
    {
        $this->title = 'Просмотр категории';

        $this->strId = $parameters['id'] ?? '';

        // Загружаем категорию
        $this->loadCategory((int)$this->strId );
        $this->prepareCategory($this->category);

        $this->content = Template::render(__DIR__ . '/../Views/v_category.php', [
            'category' => $this->category
        ]);
    }

    // Реализация абстрактного метода (не используются)
    protected function handlePostRequest(array $parameters = []): void {}
}