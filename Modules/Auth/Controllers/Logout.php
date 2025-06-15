<?php
declare(strict_types=1);

namespace Modules\Auth\Controllers;

use Modules\_base\BaseController;
use Modules\Auth\Models\Index as ModelsIndex;

class Logout extends BaseController
{
    public function __construct(array $parameters, array $user, string $pageCanonical)
    {
        parent::__construct($parameters, $user, $pageCanonical);

        $this->performLogout();
        $this->redirectToHome();
    }

    protected function performLogout(): void
    {
        $this->model = ModelsIndex::getInstance();
        $this->model ->logout();
    }

    // Реализация абстрактных методов (не используются)
    protected function handleGetRequest(array $parameters = []): void {}
    protected function handlePostRequest(array $parameters = []): void {}
}