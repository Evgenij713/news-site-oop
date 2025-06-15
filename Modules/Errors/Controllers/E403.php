<?php
declare(strict_types=1);

namespace Modules\Errors\Controllers;

use Modules\_base\BaseController;
use Modules\Errors\Models\Index as ModelsIndex;
use System\Template;

class E403 extends BaseController
{
    public function __construct(array $parameters, array $user, string $pageCanonical) {
        parent::__construct($parameters, $user, $pageCanonical);
        $this->model = ModelsIndex::getInstance();

        $this->handleRequest();
    }

    protected function handleGetRequest(array $parameters = []): void
    {
        $this->model->error403();
        $this->title = 'Страница ошибки 403';
        $this->content = Template::render(__DIR__ . '/../Views/v_403.php');
    }

    // Реализация абстрактного метода (не используются)
    protected function handlePostRequest(array $parameters = []): void {}
}