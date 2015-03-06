<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=viktor',
    'username' => 'viktor',
    'password' => '3350606',
    'charset' => 'utf8',
    'tablePrefix' => 'educom_',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 3600,
    'attributes' => [
        PDO::ATTR_PERSISTENT => TRUE,
    ],
];
