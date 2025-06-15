<?php
declare(strict_types=1);

namespace Modules\_base;

use Modules\Categories\Models\Index as ModelsCategories;

abstract class CategoryController extends BaseController {
    protected array $category = [];

    public function __construct(array $parameters, array $user, string $pageCanonical) {
        parent::__construct($parameters, $user, $pageCanonical);
        $this->model = ModelsCategories::getInstance();
    }

    protected function loadCategory(int $id): void {
        $this->category = $this->model->get($id);
        if (empty($this->category)) {
            $this->redirectTo404();
        }
    }

    protected function prepareCategory(array &$category): void
    {
        $category['name'] = htmlspecialchars($category['name']);
    }

    protected function validateCategoryFields(array $fields): array {
        $errors = $this->model->isValid($fields);
        if (!$this->model->isCategoryUnique($fields['name'])) {
            $errors[] = 'Категория с таким названием уже существует.';
        }
        return $errors;
    }

    protected function redirectIfNoPermission(): void
    {
        if (!$this->model->checkPermissions($this->role)) {
            $this->redirectTo403();
        }
    }
}