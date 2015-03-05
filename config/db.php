<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=host04',
    'username' => 'uhost04',
    'password' => 'host04pass',
    'charset' => 'utf8',
    'tablePrefix' => 'educom_',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 3600,
    'attributes' => [
        PDO::ATTR_PERSISTENT => TRUE,
    ],
];
