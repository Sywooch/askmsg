<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$db = require(__DIR__ . '/db.php');
$dbold = require(__DIR__ . '/dbold.php');
$dbtest = require(__DIR__ . '/dbtest.php');

return [
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                ],
            ],
        ],
        'db' => $db,
        'dbold' => $dbold,
//        'db' => $dbtest,
//        'dbold' => $dbtest,
    ],
];
