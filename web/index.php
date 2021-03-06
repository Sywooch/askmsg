<?php

// need to make cd vendor; ln -s bower-asset bower
// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$sBower = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'bower-asset';


/*
Yii::info('Dir : ' . $sBower . ' ' . (is_dir($sBower) ? 'exists' : ' not exists'));

if( is_dir($sBower) ) {
    Yii::setAlias('@bower', $sBower);
}
*/

Yii::setAlias('@web', __DIR__);

$config = yii\helpers\ArrayHelper::merge(
//    require(__DIR__ . '/../config/common.php'),
//    require(__DIR__ . '/../config/common-local.php'),
    require(__DIR__ . '/../config/web.php'),
    require(__DIR__ . '/../config/web-local.php')
);

(new yii\web\Application($config))->run();
