<?php

namespace app\api\modules\v1\controllers;

use yii;
use yii\filters\auth\HttpBasicAuth;
use app\models\User;
use app\api\modules\v1\models\MessageSearch;
use app\api\modules\v1\models\Message;


class MessageController extends \yii\rest\ActiveController
{

    public function init() {
        $this->modelClass = Message::className();
        parent::init();
    }

    public function actions(){
        $actions = parent::actions();
    // disable the "delete" and "create" actions
        unset($actions['delete']); // $actions['create']
        $actions['index'] = [
            'class' => 'yii\rest\IndexAction',
            'modelClass' => Message::className(),
            'checkAccess' => [$this, 'checkAccess'],
            'prepareDataProvider' =>
            function($action) {
                /** @var MessageSearch $model */
                $model = new MessageSearch();
                // TODO: тут поставить входные параметры
                return $model->searchindex([]) ;
            },
        ];
        $actions['field'] = [
            'class' => 'app\components\FieldAction',
            'modelClass' => \app\models\Message::className(),
            'checkAccess' => [$this, 'checkAccess'],
            'defaultfield' => 'msg_id',
        ];
        $actions['create']['scenario'] = 'person';
        return $actions;
    }

    /**
     * Список заголовков полей
     * @return mixed
     */
    public function actionTitle() {
        $aLabels = (new $this->modelClass())->attributeLabels();
        foreach($aLabels As $k=>$v) {
            if( (substr($k, 0, 4) !== 'msg_') && (strpos($k, '_id') === false) ) {
                unset($aLabels[$k]);
            }
        }
        return $aLabels;
    }

    public function behaviors(){
        $behaviors = parent::behaviors();

        $request = Yii::$app->request;
        $username = $request->getAuthUser();
        if( $username !== null ) {
            $user = User::findIdentityByAccessToken($username);
            if( $user ) {
                Yii::$app->user->login($user);
            }
        }

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
