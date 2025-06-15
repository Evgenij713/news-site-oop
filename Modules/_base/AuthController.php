<?php
declare(strict_types=1);

namespace Modules\_base;

use Modules\Auth\Models\Users as ModelsUsers;

abstract class AuthController extends BaseController {
    protected ModelsUsers $modelUsers;

    public function __construct(array $parameters, array $user, string $pageCanonical) {
        parent::__construct($parameters, $user, $pageCanonical);
        $this->modelUsers = ModelsUsers::getInstance();
    }

    protected function redirectIfAuthenticated(): void {
        if (!empty($this->user)) {
            $this->redirectToHome();
        }
    }
}