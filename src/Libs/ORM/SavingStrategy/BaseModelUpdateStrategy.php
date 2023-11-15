<?php

namespace Libs\ORM\SavingStrategy;

use Libs\ORM\ColumnReflectionProperty, Libs\ORM\BaseModelMetadata;

class BaseModelUpdateStrategy extends BaseModelSavingStrategy
{
    private object $original;
    /** @var ColumnReflectionProperty[]  $columnPublicPropertiesThatHaveChanged*/
    private array $columnPublicPropertiesThatHaveChanged;
    public function __construct(BaseModelMetadata $baseModelMetadata)
    {
        parent::__construct($baseModelMetadata);

        $columnPublicProperties = $this->baseModelMetadata->getInitializedColumnPublicProperties();

        $this->original = $this->baseModelMetadata->getDumpedColumnPublicPropertiesValue();

        $this->columnPublicPropertiesThatHaveChanged = array_filter($columnPublicProperties, fn($columnReflectionProperty) =>
            $this->original->{$columnReflectionProperty->getName()} != $columnReflectionProperty->getValue($this->baseModelMetadata->getUnderlyingObject()));
    }

    public function generateSQL(): string
    {
        $columnsDatabaseName = array_map(fn($reflectionProperty) => $reflectionProperty->getColumn()->databaseName, $this->columnPublicPropertiesThatHaveChanged);

        $setLiteral = implode(',', array_map(fn(string $columnDatabaseName) => "$columnDatabaseName = ?", $columnsDatabaseName));
        $setCondition = sprintf('%s = ?', $this->baseModelMetadata->getPrimaryKey()->getColumn()->databaseName);

        return sprintf(
            'UPDATE %s SET %s WHERE %s',
            $this->baseModelMetadata->getTableName(),
            $setLiteral,
            $setCondition);
    }

    public function bindPreparedStatementValue(\PDOStatement $statement): void
    {
        $placeholderNbr = 1;

        foreach ($this->columnPublicPropertiesThatHaveChanged as $columnPublicPropertyThatHaveChanged) {
            $value = $columnPublicPropertyThatHaveChanged->getValue($this->baseModelMetadata->getUnderlyingObject());
            $beforeUpdateCallback = $columnPublicPropertyThatHaveChanged->getColumn()->beforeUpdateCallback;

            if (!is_null($beforeUpdateCallback)) {
                $value = call_user_func($beforeUpdateCallback, $value);
            }

            $statement->bindValue($placeholderNbr++, $value, $columnPublicPropertyThatHaveChanged->getColumn()->pdoParam);
        }

        $statement->bindValue($placeholderNbr, $this->baseModelMetadata->getPrimaryKey()->getValue($this->baseModelMetadata->getUnderlyingObject()), $this->baseModelMetadata->getPrimaryKey()->getColumn()->pdoParam);
    }

    public function shouldAbortSaving(): bool
    {
        return empty($this->columnPublicPropertiesThatHaveChanged);
    }
}