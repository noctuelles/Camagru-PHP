CREATE TABLE users
(
    id                             integer      NOT NULL AUTO_INCREMENT,
    name                       varchar(100) NOT NULL,
    email                          varchar(254) NOT NULL,
    password                       varchar(255) NOT NULL,
    validated                      boolean      NOT NULL DEFAULT false,
    validation_code                varchar(255) NULL,
    validation_code_expiration     timestamp    NULL,
    created_at                     timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                     timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    email_notification_on_comments boolean      NOT NULL DEFAULT true,

    PRIMARY KEY (id),
    CONSTRAINT unq_users_email UNIQUE (email),
    CONSTRAINT unq_users_username UNIQUE (name)
);