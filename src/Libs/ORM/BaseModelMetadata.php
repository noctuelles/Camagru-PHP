<?php

namespace Libs\ORM;

use Libs\ORM\Attributes\Column;
use ReflectionClass, ReflectionProperty;
use Libs\ORM\Attributes\Model;

final class BaseModelMetadata
{
    private ReflectionClass $reflectionClass;
    private Model $modelAttribute;
    private array $columnPublicReflectionProperties;
    private ColumnReflectionProperty $primaryKeyColumnReflectionProperty;
    private ?object $dumpedColumnPublicProperties;

    public const PHP_TYPE_TO_PDO_PARAM_MAP = [
        'string' => \PDO::PARAM_STR,
        'bool' => \PDO::PARAM_BOOL,
        'int' => \PDO::PARAM_INT,
        'null' => \PDO::PARAM_NULL,
    ];

    /**
     * @param string|object $obj
     * @throws \Exception
     */
    public function __construct(private readonly object $underlyingModel) {
        $this->reflectionClass = new ReflectionClass($this->underlyingModel);
        $modelAttribute = current($this->reflectionClass->getAttributes(Model::class));

        if (!$modelAttribute) {
            throw new \Exception('Model '.self::class.' must have a '.Model::class.' attribute.');
        }

        $this->modelAttribute = $modelAttribute->newInstance();

        $this->setPublicPropertiesToColumn();
    }

    public function getTableName(): string {
        return $this->modelAttribute->tableName;
    }

    /**
     * @return ColumnReflectionProperty[]
     */
    public function getInitializedColumnPublicProperties(): array
    {
        return array_filter($this->columnPublicReflectionProperties, fn(ColumnReflectionProperty $reflectionProperty) => $reflectionProperty->isInitialized($this->underlyingModel));
    }

    /**
     * @return ColumnReflectionProperty[]
     */
    public function getColumnPublicProperties(): array
    {
        return $this->columnPublicReflectionProperties;
    }

    public function dumpColumnPublicPropertiesValues(): void {
        $this->dumpedColumnPublicProperties = new \stdClass();

        /** @var ReflectionProperty $columnPublicReflectionProperty */
        foreach ($this->columnPublicReflectionProperties as $columnPublicReflectionProperty) {
            if (!$columnPublicReflectionProperty->isInitialized($this->underlyingModel)) {
                continue;
            }

            $value = $columnPublicReflectionProperty->getValue($this->underlyingModel);

            $this->dumpedColumnPublicProperties->{$columnPublicReflectionProperty->getName()} = is_object($value) ? clone $value : $value;
        }
    }

    public function getDumpedColumnPublicPropertiesValue(): ?object {
        return clone $this->dumpedColumnPublicProperties;
    }

    public function getPrimaryKey(): ColumnReflectionProperty {
        return $this->primaryKeyColumnReflectionProperty;
    }

    public function getUnderlyingObject(): object {
        return $this->underlyingModel;
    }

    /**
     * @throws \ReflectionException
     */
    private function setPublicPropertiesToColumn(): void
    {
        $publicProperties = $this->reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);
        $publicProperties = array_filter($publicProperties, function(ReflectionProperty $reflectionProperty) {
            return !$reflectionProperty->isStatic();
        });

        foreach ($publicProperties as $publicProperty) {
            $publicPropertyAttributes = $publicProperty->getAttributes(Column::class);

            foreach ($publicPropertyAttributes as $publicPropertyAttribute) {
                $reflectionProperty = new ColumnReflectionProperty(
                    $this->underlyingModel,
                    $publicProperty->getName(),
                    $publicPropertyAttribute->newInstance());

                if ($reflectionProperty->getColumn()->primaryKey) {
                    $this->primaryKeyColumnReflectionProperty = $reflectionProperty;
                }

                $this->columnPublicReflectionProperties[] = $reflectionProperty;
            }
        }
    }
}