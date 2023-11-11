<?php

/**
 *
 */
class Validator
{
    const DEFAULT_VALIDATION_MESSAGE = [
        'required' => 'The field %s is required.',
        'email' => 'An valid email is required.',
        'minlen' => 'The field %s must be at least %d.',
        'between' => 'The field %s must be at least %d and at most %d.',
        'same' => 'The %s and %s must be the same.',
        'secure' => 'The %s must have between 8 and 64 characters and contain at least one number, one upper case letter, one lower case letter and one special character.',
        'unique' => 'The field %s must be unique.'
    ];

    public function __construct(private array $data, private readonly array $fields, private readonly array $fieldRulesMessage) {
    }

    public function validate(): bool
    {
        $rulesSplittingRegex = '/\s*\|\s*/';
        $onlyRulesMessage = array_filter($this->fieldRulesMessage, fn($message) => is_string($message));
        $rulesMessage = array_merge(Validator::DEFAULT_VALIDATION_MESSAGE, $onlyRulesMessage);
        $errorMessages = [];

        foreach ($this->fields as $field => $option) {
            $rules = preg_split($rulesSplittingRegex, $option, -1, PREG_SPLIT_NO_EMPTY);

            foreach ($rules as $rule) {
                [$ruleName, $parameters] = $this->getRuleNameAndParameters($rule);
                $ruleValidationFunction = 'is' . ucfirst(strtolower($ruleName));

                if (is_callable([$this, $ruleValidationFunction])) {
                    $passingValidation = $this->$ruleValidationFunction($field, ...$parameters);

                    if (!$passingValidation) {
                        $errorMessages[$field] = sprintf($this->fieldRulesMessage[$field][$ruleName] ?? $rulesMessage[$ruleName],
                            $field, ...$parameters);
                    }
                }
            }
        }

        return $errorMessages;
    }

    private function getRuleNameAndParameters(string $rule): array
    {
        if (preg_match("/([^:]+):([^:]+)/", $rule, $matches)) {
            $ruleName = $matches[1];
            $parameters = preg_split('/\s*,\s*/', $matches[2]);
        } else {
            $ruleName = $rule;
            $parameters = [];
        }

        return [$ruleName, $parameters];
    }

    private function isRequired(string $field): bool
    {
        if (isset($this->data[$field]) && $this->data[$field] != '') {
            return true;
        }

        return false;
    }

    private function isMinlen(string $field, int $minLength): bool
    {
        if (isset($this->data[$field]) && strlen($this->data[$field]) >= $minLength) {
            return true;
        }

        return false;
    }

    private function isMaxlen(string $field, int $maxLength): bool
    {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $maxLength) {
            return true;
        }

        return false;
    }

    private function isBetween(string $field, int $minLength, int $maxLength): bool
    {
        if (!isset($this->data[$field])) {
            return false;
        }

        $fieldLen = strlen($this->data[$field]);

        if ($fieldLen >= $minLength && $fieldLen <= $maxLength) {
            return true;
        }

        return false;
    }

    private function isEmail(string $field): bool
    {
        if (isset($this->data[$field]) && filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }

    private function isSame(string $field, string $as): bool
    {
        return $this->data[$field] === $this->data[$as];
    }

    private function isUnique(string $field, string $table, string $column): bool
    {
        global $pdo;

        $sql = "SELECT username FROM $table WHERE $column = ?";
        $statement = $pdo->prepare($sql);
        $statement->execute([$this->data[$field]]);

        return $statement->fetchColumn() === false;
    }

    private function isSecure(string $field): bool
    {
        if (!isset($this->data[$field])) {
            return false;
        }

        $pattern = "#.*^(?=.{8,64})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#";
        return preg_match($pattern, $this->data[$field]);
    }
}