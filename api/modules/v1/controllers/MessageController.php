<?php

namespace app\api\modules\v1\controllers;

use app\models\Message;
use yii\filters\auth\HttpBasicAuth;


class MessageController extends \yii\rest\ActiveController
{

    public function init() {
        $this->modelClass = Message::className();
        parent::init();
    }

    public function actions(){
        $actions = parent::actions();
    // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['create']);
        return $actions;
    }

    public function behaviors(){
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::className(),
        ];
        return $behaviors;
    }

/*
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionView($id)
    {
        return User::findOne($id);
    }
*/

}
