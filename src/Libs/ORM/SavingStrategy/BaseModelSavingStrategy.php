<?php

namespace Libs\ORM\SavingStrategy;

use Libs\ORM\BaseModelMetadata;

abstract class BaseModelSavingStrategy
{
    public function __construct(protected readonly BaseModelMetadata $baseModelMetadata)
    {
    }

    public abstract function generateSQL(): string;

    public abstract function bindPreparedStatementValue(\PDOStatement $statement): void;
    public function shouldAbortSaving(): bool {
        return false;
    }
}