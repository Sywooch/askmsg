<?php

namespace app\api\modules\v1\controllers;

use yii;
use yii\filters\auth\HttpBasicAuth;
use app\models\User;
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
        return $actions;
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

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return array_merge(
            parent::verbs(),
            ['options' => ['OPTIONS']]
        );
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
