<?php

namespace app\api\modules\v1;

use yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\api\modules\v1\controllers';

    public function init()
    {
        parent::init();
        Yii::$app->user->enableSession = false;
        Yii::$app->user->loginUrl = null;
        Yii::$app->user->enableAutoLogin = false;
        Yii::$app->user->autoRenewCookie = false;

        // custom initialization code goes here
    }
}
