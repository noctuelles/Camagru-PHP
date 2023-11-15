<?php

namespace Libs\ORM\SavingStrategy;

use Libs\ORM\ColumnReflectionProperty, Libs\ORM\BaseModelMetadata;

class BaseModelInsertStrategy extends BaseModelSavingStrategy
{
    /** @var ColumnReflectionProperty[] $columnPublicProperties */
    private array $columnPublicProperties;
    public function __construct(BaseModelMetadata $baseModelMetadata)
    {
        parent::__construct($baseModelMetadata);

        $this->columnPublicProperties = $this->baseModelMetadata->getInitializedColumnPublicProperties();
    }

    public function generateSQL(): string
    {
        $columnsDatabaseName = array_map(fn(ColumnReflectionProperty $reflectionProperty) => $reflectionProperty->getColumn()->databaseName, $this->columnPublicProperties);
        $columnsDatabaseNameStr = implode(',', $columnsDatabaseName);

        $placeHolders = array_fill(0, count($columnsDatabaseName), '?');
        $placeHoldersStr = implode(',', $placeHolders);

        return sprintf("INSERT INTO %s (%s) VALUES (%s)",
            $this->baseModelMetadata->getTableName(),
            $columnsDatabaseNameStr,
            $placeHoldersStr);
    }

    public function bindPreparedStatementValue(\PDOStatement $statement): void
    {
        foreach ($this->columnPublicProperties as $index => $columnPublicProperty) {
            $value = $columnPublicProperty->getValue($this->baseModelMetadata->getUnderlyingObject());
            $beforeInsertCallback = $columnPublicProperty->getColumn()->beforeInsertCallback;

            if (!is_null($beforeInsertCallback)) {
                $value = call_user_func($beforeInsertCallback, $value);
            }

            $statement->bindValue($index, $value, $columnPublicProperty->getColumn()->pdoParam);
        }
    }

    public function shouldAbortSaving(): bool
    {
        return empty($this->columnPublicProperties);
    }
}