<?php

use Libs\ORM\BaseModel;

require_once 'Libs/Database.php';

spl_autoload_register(static function(string $className) {
    $classPath = str_replace('\\', '/', $className);
    $filePath = __DIR__ . '/' . $classPath . '.php';

    echo "Requiring " . $filePath . PHP_EOL;

    if (file_exists($filePath)) {
        require $filePath;
    }
});

global $pdo;

BaseModel::$PDO = $pdo;

$user = new Models\User();

$user->email = 'paul@caca.fr';
$user->password = 'prout';
$user->name = 'Paul le con';