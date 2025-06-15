<?php
declare(strict_types=1);

namespace Modules\Errors\Models;

class Index {
	protected static $instance;

    public static function getInstance() : static{
        if(static::$instance === null){
            static::$instance = new static();
        }

        return static::$instance;
    }

    protected function __construct(){
    }

    public function error404(): void {
        header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found', true, 404);
    }

    public function error403(): void {
        header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden', true, 403);
    }
}