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

    /**
     * Данные по одному полю
     *
     * @param $id
     * @return mixed
     *
     */
/*
    public function actionField($id) {
        $model = $this->runAction('view', ['id' => $id]);
        Yii::info('$model = ' . print_r($model, true));
        $sFld = Yii::$app->request->getQueryParam('name', 'msg_id');
        $data = null;
        if( $model !== null ) {
            $data = isset($model->attributes[$sFld]) ? $model->attributes[$sFld] : '';
        }
        return $data;
    }
*/
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

        /*
                $behaviors['authenticator'] = [
                    'class' => HttpBasicAuth::className(),
                    'auth' => function ($username, $password) {
                        Yii::info(print_r(Yii::$app->user, true));
                        $user = null;
                        if( $password === null ) {
                            if( $username !== null ) {
                                $user = User::findIdentityByAccessToken($username);
                                if( $user ) {
                                    Yii::$app->user->login($user);
                                }
                            }
                        }
                        else if( $username !== null ) {
                            $user = User::findByUsername($username);
                            if( $user ) {
                                if( $user->validatePassword($password) ) {
                                    Yii::$app->user->login($user);
                                }
                            }
                        }
                        return $user;
                    },
                ];
        */
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
