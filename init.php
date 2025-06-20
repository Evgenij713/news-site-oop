<?php
declare(strict_types=1);

spl_autoload_register(function($name){
    $path = str_replace('\\', '/', $name) . '.php';

    if(file_exists($path)){
        include_once($path);
    }
    // else, error, mb spl_autoload_register later
});