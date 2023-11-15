<?php

namespace Libs\ORM\Attributes;

use Attribute;

#[Attribute]
final class Model
{
    public function __construct(public string $tableName)
    {
    }
}