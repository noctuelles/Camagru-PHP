<?php

function isRequired(array $data, string $field) {
    if (isset($data[$field]) && $data[$field] != '') {
        return true;
    }

    return false;
}

function isMinlen(array $data, string $field, int $minLength) {
    if (isset($data[$field]) && strlen($data[$field]) >= $minLength) {
        return true;
    }

    return false;
}

function isMaxlen(array $data, string $field, int $maxLength) {
    if (isset($data[$field]) && strlen($data[$field]) < $maxLength) {
        return true;
    }

    return false;
}

function isEmail(array $data, string $field) {
    if (isset($data[$field]) && filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
        return true;
    }

    return false;
}

function getRuleNameAndParameters(string $rule): array {
    if (preg_match("/([^:]+):([^:]+)/", $rule, $matches)) {
        $ruleName = $matches[1];
        $parameters = preg_split('/\s*,\s*/', $matches[2]);
    } else {
        $ruleName = $rule;
        $parameters = [];
    }

    return [$ruleName, $parameters];
}
function validate(array $data, array $fields) {
    $rulesSplittingRegex = '/\s*\|\s*/';

    foreach($fields as $field => $option) {
        $rules = preg_split($rulesSplittingRegex, $option, -1, PREG_SPLIT_NO_EMPTY);

        foreach($rules as $rule) {
            [$ruleName, $parameters] = getRuleNameAndParameters($rule);
            $ruleValidationFunction = 'is' . ucfirst(strtolower($ruleName));

            if (is_callable($ruleValidationFunction)) {
                $passingValidation = $ruleValidationFunction($data, $field, ...$parameters);

                if (!$passingValidation) {
                    echo $field . " not passing validation.\n";
                }
            }
        }
    }
}