<?php
declare(strict_types=1);

namespace Modules\_base;

use System\Contracts\IController;
use System\Template;

abstract class BaseController implements IController {
    protected string $title = '';
    protected string $content = '';
    protected string $canonical = '';
    protected array $user = [];
    protected int $role;
    protected $model;
    protected string $strId = '';

    public function __construct(array $parameters, array $user, string $pageCanonical) {
        $this->user = $user;
        $this->canonical = $pageCanonical;
        $this->role = $user['role'] ?? 0;
    }

    public function render(): string {
        return Template::render(__DIR__ . '/v_main.php', [
            'title' => $this->title,
            'content' => $this->content,
            'canonical' => $this->canonical,
            'user' => $this->user,
            'usersRole' => $this->role,
            'controller' => static::class
        ]);
    }

    protected function redirectTo404(): void {
        header('Location:' . BASE_URL . 'e404');
        exit();
    }

    protected function redirectTo403(): void {
        header('Location:' . BASE_URL . 'e403');
        exit();
    }

    protected function redirectToHome(): void {
        header('Location:' . BASE_URL);
        exit();
    }

    protected function handleRequest(array $parameters = []): void {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->handleGetRequest($parameters);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostRequest($parameters);
        }
    }

    abstract protected function handleGetRequest(array $parameters = []): void;
    abstract protected function handlePostRequest(array $parameters = []): void;

    protected function jsonResponse(array $response): void {
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function __call(string $name, array $arguments) {
        $this->redirectTo404();
    }
}