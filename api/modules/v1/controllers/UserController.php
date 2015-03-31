<?php

namespace app\api\modules\v1\controllers;

use yii;
use app\api\modules\v1\models\UserSearch;
use app\api\modules\v1\models\User;


class UserController extends \yii\rest\ActiveController
{

    public function init() {
        $this->modelClass = User::className();
        parent::init();
    }

    public function actions(){
        $actions = parent::actions();
    // disable the "delete" and "create" actions
        unset($actions['delete']); // $actions['create']
        $actions['index'] = [
            'class' => 'yii\rest\IndexAction',
            'modelClass' => User::className(),
            'checkAccess' => [$this, 'checkAccess'],
            'prepareDataProvider' =>
            function($action) {
                /** @var UserSearch $model */
                $model = new UserSearch();
                // TODO: тут поставить входные параметры
                return $model->search([]) ;
            },
        ];
        $actions['field'] = [
            'class' => 'app\components\FieldAction',
            'modelClass' => \app\models\User::className(),
            'checkAccess' => [$this, 'checkAccess'],
            'defaultfield' => 'us_id',
        ];
//        $actions['create']['scenario'] = 'person';
        return $actions;
    }

    /**
     * Список заголовков полей
     * @return mixed
     */
    public function actionTitle() {
        $aLabels = (new $this->modelClass())->attributeLabels();
        foreach($aLabels As $k=>$v) {
            if( (substr($k, 0, 3) !== 'us_') && (strpos($k, '_id') === false) ) {
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
            $user = \app\models\User::findIdentityByAccessToken($username);
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
