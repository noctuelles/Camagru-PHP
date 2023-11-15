<?php

namespace Libs;

use PDO, PDOException;

$host = 'mysql-server';
$dbname = 'camagru';
$user = 'camagru';
$password = 'bla';
$chrs = 'utf8mb4';
$attr = "mysql:host=$host;dbname=$dbname;charset=$chrs";
$opts =
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

try {
    $pdo = new PDO($attr, $user, $password, $opts);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
