<?php

use yii\helpers\ArrayHelper;

$params = ArrayHelper::merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

$config = [
    'id' => 'basic',
    'name' => 'Обращения к руководителю Департамента образования города Москвы',
    'basePath' => dirname(__DIR__),
    'language' => 'ru',
    'bootstrap' => ['log'],
    'defaultRoute' => 'message/create',
    'modules' => [
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ],
        'datecontrol' =>  [
            'class' => 'kartik\datecontrol\Module',
             // format settings for displaying each date attribute (ICU format example)
            'displaySettings' => [
                kartik\datecontrol\Module::FORMAT_DATE => 'dd.MM.yyyy',
                kartik\datecontrol\Module::FORMAT_TIME => 'HH:mm:ss a',
                kartik\datecontrol\Module::FORMAT_DATETIME => 'dd.MM.yyyy HH:mm:ss a',
            ],
            // format settings for saving each date attribute (PHP format example)
            'saveSettings' => [
                kartik\datecontrol\Module::FORMAT_DATE => 'php:Y-m-d',
                kartik\datecontrol\Module::FORMAT_TIME => 'php:H:i:s',
                kartik\datecontrol\Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
            ],
        ],
        'v1' => [
            'class' => 'app\api\modules\v1\Module',
        ],
    ],
    'components' => [
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@app/themes/fromvideo'
                ],
                'baseUrl' => '@web/themes/fromvideo',
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'Wxk_V47W24rzFLXkrfnK96DJgR82fZFL',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'authManager' => [
//            'class' => 'yii\rbac\PhpManager',
            'class' => 'app\components\PhpextManager',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
            'viewPath' => '@app/views/mail',
            'htmlLayout' => false,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'cache' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/user', 'v1/message', ],
                    'except' => ['delete'],
                    'extraPatterns' => [
                        'GET' => 'title',
                        'GET field/{id}' => 'field',
                    ],
                ],
/*
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v2/user', 'v2/post']
                ],
*/
                'upload/files/<dname:\d+>/<fname:[\w\-\.]+>' => 'file/getfile',
                '<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>' => '<_c>/<_a>',
                '<_c:[\w\-]+>/<id:\d+>' => '<_c>/view',
                '<_c:[\w\-]+>/<_a:[\w\-]+>' => '<_c>/<_a>',
                '<_c:[\w\-]+>' => '<_c>/index',
            ],
//            'enableStrictParsing' => true,
                /*
            'rules' => [
                                '<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>' => '<_m>/<_c>/<_a>',
                                '<_c:[\w\-]+>/<id:\d+>' => '<_m>/<_c>/view',
                                '<_c:[\w\-]+>/<_a:[\w\-]+>' => '<_c>/<_a>',
                                '<_c:[\w\-]+>' => '<_c>/index',
                                '<_m:[\w\-]+>/<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>' => '<_m>/<_c>/<_a>',
                                '<_m:[\w\-]+>/<_c:[\w\-]+>/<id:\d+>' => '<_m>/<_c>/view',
                                '<_m:[\w\-]+>' => '<_m>/default/index',
                                '<_m:[\w\-]+>/<_c:[\w\-]+>' => '<_m>/<_c>/index',
            ],
                */
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
/*
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                ],
            ],
*/
        ],
//        'db' => require(__DIR__ . '/db.php'),
    ],
    'params' => $params,
];

return $config;
