<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SignupForm;
use app\models\ConfirmEmailForm;
use app\models\ResetPasswordForm;
use app\models\PasswordResetRequestForm;

use app\models\User;
use app\models\Message;
use app\models\Regions;
use app\models\Rolesimport;
use Httpful\Request;
use Httpful\Response;
use app\components\WaitTrait;

class SiteController extends Controller
{
    use WaitTrait;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
/*
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
*/
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index1');
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) ) {

            $this->DoDelay('loginform.delay.time');

            if( $model->login() ) {
                if (Yii::$app->user->can(Rolesimport::ROLE_ADMIN)) {
                    return $this->redirect(['message/admin']);
                } elseif (Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM)) {
                    return $this->redirect(['message/moderatelist']);
                } elseif (Yii::$app->user->can(Rolesimport::ROLE_ANSWER_DOGM)) {
                    return $this->redirect(['message/answerlist']);
                } else {
                    return $this->redirect(['message/list']);
                }
                return $this->goBack();
            }
        } // else {
            return $this->render('login', [
                'model' => $model,
            ]);
//        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    /*
     * Форма регистрации
     *
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    Yii::$app->getSession()->setFlash('success', 'Подтвердите ваш электронный адрес.');
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /*
     * Форма сброса пароля
     *
     */
    public function actionRequestpasswordreset()
    {
        $model = new PasswordResetRequestForm();
        if ( $model->load(Yii::$app->request->post()) ) {
            $this->DoDelay('restoreform.delay.time');

            if( $model->validate() ) {
                if ($model->sendEmail()) {
                    Yii::$app->getSession()->setFlash('success', 'Спасибо! На ваш Email было отправлено письмо со ссылкой на восстановление пароля.');
                    return $this->goHome();
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Извините. У нас возникли проблемы с отправкой письма восстановления пароля.');
                }
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /*
     * Сброс пароля
     *
     * @param string $token
     *
     *
     */
    public function actionResetpassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'Ваш пароль успешно изменен');
            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /*
     * Подтверждение email
     *
     * @param string $token
     *
     */
    public function actionConfirmemail($token)
    {
        try {
            $model = new ConfirmEmailForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->confirmEmail()) {
            Yii::$app->getSession()->setFlash('success', 'Спасибо! Ваш Email успешно подтверждён.');
        } else {
            Yii::$app->getSession()->setFlash('error', 'Ошибка подтверждения Email.');
        }

        return $this->goHome();
    }

    /*
     * Вставка фейковых записей
     * сначала генерим фикстуры php yii fixture/generate-all --count=10
     * потом выполняем этот экшен, чтобы получить тестовые данные
     * https://github.com/fzaninotto/Faker - полный список вариантов
     * https://github.com/yiisoft/yii2/tree/master/extensions/faker - как в yii запускать
     */
    public function actionFakedata()
    {
        $sDir = \Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'unit' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR;
        $a = [
            User::classname() => 'user.php',
            Regions::classname() => 'reg.php',
            Message::classname() => 'msg.php',
        ];
        $sOut = '';
        foreach ($a as $k => $v) {
            
            $data = require($sDir . $v);
            foreach($data As $at) {
                $o = new $k();
                $o->attributes = $at;
                if( !$o->save() ) {
                    $sOut .= str_replace("\n", "<br />\n", print_r($o->getErrors(), true));
                }
            }
        }
        return $sOut;
    }

    /*
     * Тест получения данных с api
     */
    public function actionTesthttp()
    {
        if( false ) {
            $id = 12241;
            $sOut = 'No result';
            if ($id > 0) {
                $data = [
                    'filters' => [
                        'eo_id' => $id,
                    ],
                    'maskarade' => [
                        'eo_id' => "id",
                        'eo_short_name' => "text",
                    ],
                    'fields' => implode(";", ["eo_id", "eo_short_name", "eo_district_name_id"]),
                ];
                $request = Request::post('http://hastur.temocenter.ru/task/eo.search/')// , http_build_query($data), 'application/x-www-form-urlencoded'
                ->addHeader('Accept', 'application/json; charset=UTF-8')
                    ->body(http_build_query($data))
                    ->contentType('application/x-www-form-urlencoded');

                /** @var Response $response */
                $response = $request->send();
                $aData = json_decode($response->body, true);
                $ob = null;
                if ($aData['total'] > 0) {
                    $ob = array_pop($aData['list']);
                }
                $sOut = print_r($ob, true);
            }
            return /* $id . */ str_replace("\n", "<br />", htmlspecialchars($sOut));
        }
/*
        $oldConnection = Yii::$app->dbold;
        $sql = 'Select m.ID As MSGID, m.*, p.*, a.*, a.VALUE As dopuser '
            . 'From b_iblock_element_prop_s52 p, b_iblock_element m '
            . 'Left Outer Join b_iblock_element_prop_m52 a On a.IBLOCK_ELEMENT_ID = m.ID '
            . 'Where m.IBLOCK_ID = 52 And p.IBLOCK_ELEMENT_ID = m.ID And LENGTH(m.PREVIEW_TEXT) > 0 Limit 20'; //  And m.ID > 82510 Order By m.ID Limit 20

        $aMsg = $oldConnection->createCommand($sql)->query();
        $nCount = $aMsg->count();
        echo '<p>message get ' . $nCount . " records</p>\n";
        foreach($aMsg As $ad) {
            echo $ad['PROPERTY_194'] . ' ' . $ad['PROPERTY_195'] . ' ' . (($ad['PROPERTY_196'] === null) ? '' : $ad['PROPERTY_196']) . "<br />\n";
        }
*/


    }

}
