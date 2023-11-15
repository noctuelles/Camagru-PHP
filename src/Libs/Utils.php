<?php

namespace Libs;
function redirectTo(string $page) {
    header("Location: $page");
    die();
}

function renderView(string $filename, array $data = []) {
    extract($data);
    require_once(__DIR__ . "/../Views/$filename.php");
}

function arrayFind(array $array, callable $cb): mixed {
    foreach($array as $item) {
        if (call_user_func($cb) === true) {
            return $item;
        }
    }
    return null;
}