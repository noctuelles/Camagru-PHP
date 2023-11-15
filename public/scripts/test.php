<?php

$myVar = '123';
var_dump($myVar);

$result = filter_var($myVar, FILTER_VALIDATE_INT);
var_dump($result);