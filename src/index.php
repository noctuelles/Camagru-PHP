<?php

use Libs\ORM\BaseModel;

spl_autoload_register(static function(string $className) {
    $classPath = str_replace('\\', '/', $className);
    $filePath = __DIR__ . '/' . $classPath . '.php';

    echo "Requiring " . $filePath . PHP_EOL;

    if (file_exists($filePath)) {
        require $filePath;
    }
});

$requestPath = $_SERVER['PATH_INFO'];

if (substr($requestPath, 1, 5) === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        require_once 'Pages/Login.php';
    } else {
        echo 'Post';
    }
} else {
    header('Location: login');
    echo 'Not found';
}

