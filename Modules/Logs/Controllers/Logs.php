<?php
declare(strict_types=1);

namespace Modules\Logs\Controllers;

use Modules\_base\BaseController;
use Modules\Logs\Models\Index as ModelsIndex;
use System\Template;

class Logs extends BaseController
{
    public function __construct(array $parameters, array $user, string $pageCanonical) {
        parent::__construct($parameters, $user, $pageCanonical);
        $this->model = ModelsIndex::getInstance();
        $this->redirectIfNoPermission();

        $this->handleRequest($parameters);
    }

    protected function handleRequest(array $parameters = []): void {
        $date = $parameters['date'] ?? '';

        $this->title = 'Просмотр логов';
        $this->content = Template::render(__DIR__ . '/../Views/v_admin.php', [
            'date' => $date
        ]);

        // Проверяем, является ли запрос AJAX-запросом
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $this->handleAjaxRequest($date);
        }
    }

    protected function handleAjaxRequest(string $date): void {
        $logs = $this->model->getLogs($date);
        foreach ($logs as $key => $log) {
            $logs[$key]['referer_danger'] = ($this->model->isValidUrl($log['referer']) ? 0 : 1);
            $logs[$key]['uri_danger'] = ($this->model->isValidUrl($log['uri']) ? 0 : 1);
        }

        echo Template::render(__DIR__ . '/../Views/v_logs.php', [
            'logs' => $logs
        ]);
        exit();
    }

    protected function redirectIfNoPermission(): void
    {
        if (!$this->model->checkPermissions($this->role)) {
            $this->redirectTo403();
        }
    }

    // Реализация абстрактных методов (не используются)
    protected function handleGetRequest(array $parameters = []): void {}
    protected function handlePostRequest(array $parameters = []): void {}
}