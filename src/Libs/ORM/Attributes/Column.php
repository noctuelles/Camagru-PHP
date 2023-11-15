<?php

namespace Libs\ORM\Attributes;

use PDO;
use Closure;
use Attribute;

#[Attribute]
final class Column
{
    public ?Closure
        $afterFetchCallback = null,
        $beforeInsertCallback = null,
        $beforeUpdateCallback = null;
    public int $pdoParam = PDO::PARAM_STR;

    public function __construct(
        public bool $primaryKey = false,
        public ?string $databaseName = null,
        ?string $afterFetch = null,
        ?string $beforeInsert = null,
        ?string $beforeUpdate = null)
    {
        $this->registerLifecycleCallback('afterFetchCallback', $afterFetch);
        $this->registerLifecycleCallback('beforeInsertCallback', $beforeInsert);
        $this->registerLifecycleCallback('beforeUpdateCallback', $beforeUpdate);
    }

    private function registerLifecycleCallback(string $lifecycle, ?string $functionName): void
    {
        if (is_null($functionName)) {
            return;
        }

        if (!is_callable($functionName)) {
            throw new \InvalidArgumentException("The function $functionName() is not callable.");
        }

        $this->$lifecycle = fn(mixed $value) => call_user_func($functionName, $value);
    }
}