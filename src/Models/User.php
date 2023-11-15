<?php

namespace Models;

use DateTime;
use Libs\ORM\Attributes\Column;
use Libs\ORM\Attributes\Model;
use Libs\ORM\BaseModel;

function hashPassword(string $value): string
{
    return password_hash($value, PASSWORD_ARGON2I);
}

function hashValidationCode(string $rawValidationCode): string
{
    return password_hash($rawValidationCode, PASSWORD_BCRYPT);
}

#[Model(tableName: 'users')]
class User extends BaseModel
{
    #[Column(primaryKey: true)]
    public int $id;
    #[Column()]
    public string $name;
    #[Column()]
    public string $email;
    #[Column(beforeInsert: 'Models\hashPassword', beforeUpdate: 'Models\hashPassword')]
    public string $password;
    #[Column()]
    public bool $validated;
    #[Column(databaseName: 'validation_code', beforeInsert: 'Models\hashValidationCode')]
    public ?string $validationCode;
    #[Column(databaseName: 'validation_code_expiration')]
    public ?DateTime $validationCodeExpiration;
    #[Column(databaseName: 'created_at')]
    public DateTime $createdAt;
    #[Column(databaseName: 'updated_at')]
    public DateTime $updatedAt;
    #[Column(databaseName: 'email_notification_on_comments')]
    public bool $emailNotificationOnComments;
}