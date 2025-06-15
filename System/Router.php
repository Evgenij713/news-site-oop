<?php
declare(strict_types=1);

namespace System;

use System\Contracts\IRouter;
use System\Exceptions\ExcFatal;

class Router implements IRouter {
    protected string $intGT0 = '[1-9]+\d*';
    protected string $normDate = '\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])';
    protected array $routes = [];

    public function __construct() {
        $this->routes = [
            [
                'test' => '/^$/',
                'controller' => 'Modules\Articles\Controllers\Articles'
            ],
            [
                'test' => "/^article\/($this->intGT0)\/?$/",
                'controller' => 'Modules\Articles\Controllers\Article',
                'params' => ['id' => 1]
            ],
            [
                'test' => '/^article\/add\/?$/',
                'controller' => 'Modules\Articles\Controllers\AddArticle'
            ],
            [
                'test' => "/^article\/edit\/($this->intGT0)\/?$/",
                'controller' => 'Modules\Articles\Controllers\EditArticle',
                'params' => ['id' => 1]
            ],
            [
                'test' => "/^articles\/category\/($this->intGT0)\/?$/",
                'controller' => 'Modules\Articles\Controllers\CategoryArticles',
                'params' => ['id' => 1]
            ],
            [
                'test' => '/^register\/?$/',
                'controller' => 'Modules\Auth\Controllers\Register'
            ],
            [
                'test' => '/^login\/?$/',
                'controller' => 'Modules\Auth\Controllers\Login'
            ],
            [
                'test' => '/^logout\/?$/',
                'controller' => 'Modules\Auth\Controllers\Logout'
            ],
            [
                'test' => "/^categories\/?$/",
                'controller' => 'Modules\Categories\Controllers\Categories'
            ],
            [
                'test' => "/^category\/($this->intGT0)\/?$/",
                'controller' => 'Modules\Categories\Controllers\Category',
                'params' => ['id' => 1]
            ],
            [
                'test' => '/^category\/add\/?$/',
                'controller' => 'Modules\Categories\Controllers\AddCategory'
            ],
            [
                'test' => "/^category\/edit\/($this->intGT0)\/?$/",
                'controller' => 'Modules\Categories\Controllers\EditCategory',
                'params' => ['id' => 1]
            ],
            [
                'test' => '/^e403\/?$/',
                'controller' => 'Modules\Errors\Controllers\E403'
            ],
            [
                'test' => '/^e404\/?$/',
                'controller' => 'Modules\Errors\Controllers\E404'
            ],
            [
                'test' => '/^logs\/?$/',
                'controller' => 'Modules\Logs\Controllers\Logs'
            ],
            [
                'test' => "/^logs\/($this->normDate)\/?$/",
                'controller' => 'Modules\Logs\Controllers\Logs',
                'params' => ['date' => 1]
            ],
        ];
    }

    /**
     * @throws ExcFatal
     */
    public function parseUrl(string $url): array{
        $res = [];

        foreach($this->routes as $route) {
            $matches = [];

            if(preg_match($route['test'], $url, $matches)) {
                $res['controller'] = $route['controller'];

                if(isset($route['params'])) {
                    foreach($route['params'] as $name => $num) {
                        $res['params'][$name] = $matches[$num];
                    }
                }

                break;
            }
        }

        if (empty($res)) {
            throw new ExcFatal('controller not found');
        }

        return $res;
    }

    // Если в url есть лишние слэши, делаем редирект на url без них
    public function findPath(string $url) : void {
        if ($url !== BASE_URL && !$this->checkUrl($url)) {
            header('Location:' . $this->conversionUrl($url), true, 301);
            exit();
        }
    }

    protected function conversionUrl(string $url): string {
        // Удаляем все лишние слэши
        $url = preg_replace('~/+~', '/', $url);
        // Удаляем слэш в конце, если он есть
        return rtrim($url, '/');
    }

    protected function checkUrl(string $url): bool {
        if (str_contains($url, '//') === true || $url[strlen($url)-1] === '/') {
            return false;
        }
        return true;
    }
}