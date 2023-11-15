<?php

namespace Libs\ORM;

use ReflectionException;
use ReflectionProperty;
use Libs\ORM\Attributes\Column;

final class ColumnReflectionProperty extends ReflectionProperty
{
    /**
     * @throws ReflectionException
     */
    public function __construct(object|string $class, string $property, private readonly Column $column)
    {
        parent::__construct($class, $property);

        if (is_null($this->column->databaseName)) {
            $this->column->databaseName = $this->getName();
        }

        if (is_null($this->getType())) {
            return;
        }

        $this->column->pdoParam = BaseModelMetadata::PHP_TYPE_TO_PDO_PARAM_MAP[$this->getType()->getName()] ?? \PDO::PARAM_STR;
    }

    public function getColumn(): Column
    {
        return $this->column;
    }
}