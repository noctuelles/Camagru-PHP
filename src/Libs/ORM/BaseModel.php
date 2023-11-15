<?php

namespace Libs\ORM;

use PDO;
use Libs\ORM\SavingStrategy\BaseModelUpdateStrategy, Libs\ORM\SavingStrategy\BaseModelInsertStrategy;

abstract class BaseModel
{
    public static PDO $PDO;
    public bool $persisted = false;
    private BaseModelMetadata $baseModelMetadata;

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function __construct() {
        $this->baseModelMetadata = new BaseModelMetadata($this);
    }

    public final function save(): void
    {
        if ($this->persisted) {
            $baseModelSavingStrategy = new BaseModelUpdateStrategy($this->baseModelMetadata);
        } else {
            $baseModelSavingStrategy = new BaseModelInsertStrategy($this->baseModelMetadata);
        }

        if ($baseModelSavingStrategy->shouldAbortSaving()) {
            return ;
        }

        $statement = BaseModel::$PDO->prepare($baseModelSavingStrategy->generateSQL());
        $baseModelSavingStrategy->bindPreparedStatementValue($statement);
        $statement->execute();

        $this->baseModelMetadata->dumpColumnPublicPropertiesValues();

        if (!$this->persisted) {
            $this->persisted = true;
            $this->baseModelMetadata->getPrimaryKey()->setValue($this, BaseModel::$PDO->lastInsertId());
        }
    }

    /**
     * @throws \Exception
     */
    public static function findBy(string $column, mixed $value, string|array $selectedColumns): BaseModel | null {
        $calledClass = get_called_class();
        $model = new $calledClass();
        $baseModelMetadata = new BaseModelMetadata($model);

        $selectedColumns = is_array($selectedColumns) ? implode(',', $selectedColumns) : $selectedColumns;
        $sql = sprintf("SELECT %s FROM %s WHERE %s = ?", $selectedColumns, $baseModelMetadata->getTableName(), $column);
        $statement = BaseModel::$PDO->prepare($sql);
        $statement->bindValue(0, $value, BaseModelMetadata::PHP_TYPE_TO_PDO_PARAM_MAP[gettype($value)]);
        $statement->execute();

        $fetchedRow = $statement->fetch();

        if (!$fetchedRow) {
            return null;
        }

        foreach ($baseModelMetadata->getColumnPublicProperties() as $columnPublicProperty) {
            $publicPropertyDbName = $columnPublicProperty->getColumn()->databaseName;

            if (isset($fetchedRow[$publicPropertyDbName]) || $fetchedRow[$publicPropertyDbName] === null) {
                $columnPublicProperty->setValue($model, $fetchedRow[$publicPropertyDbName]);
            }
        }

        return $model;
    }
}