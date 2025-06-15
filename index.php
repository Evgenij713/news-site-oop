<?php
declare(strict_types=1);
session_start();

use System\Router;
use Modules\Auth\Models\Index as ModelsAuth;
use Modules\Logs\Models\Index as ModelsLogs;
use System\Exceptions\ExcFatal;

include_once('init.php');
const HOST = 'http://localhost';
const BASE_URL = '/oop-8/';

const DB_HOST = 'localhost';
const DB_NAME = 'news';
const DB_USER = 'root';
const DB_PASS = '';
const DB_CHARSET = 'utf8mb4';
const SITE_CHARSET = 'UTF-8';

try {
    $router = new Router();
    $modelAuth = ModelsAuth::getInstance();

    $user = $modelAuth->authGetUser();

    $pageCanonical = HOST . BASE_URL;

    $uri = $_SERVER['REQUEST_URI'];

    $router->findPath($uri);

    $badUrl = BASE_URL . 'index.php';

    if (str_starts_with($uri, $badUrl)) {
        header('Location:' . BASE_URL . 'e404');
        exit();
    }
    else {
        $modelLogs = ModelsLogs::getInstance();
        $modelLogs->saveLog();

        $url = $_GET['querysystemurl'] ?? '';

        $routerRes = $router->parseUrl($url);
        $cname = $routerRes['controller'];
        $parameters = $routerRes['params'] ?? [];

        $urlLen = strlen($url);
        if($urlLen > 0 && $url[$urlLen - 1] === '/'){
            $url = substr($url, 0, $urlLen - 1);
        }
        $pageCanonical .= $url;

        $controller = new $cname($parameters, $user, $pageCanonical);
        $html = $controller->render();
        echo $html;
    }
}
catch(ExcFatal $e){
    echo 'Fatal error - ' . $e->getMessage();
}