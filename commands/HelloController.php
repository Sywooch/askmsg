<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\User;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";
    }

    public function actionInit($password = '1111', $template = '', $subject='')
    {
        $model = new User();
        $model->attributes = [
            'us_login' => 'user_' . date('ymdHis'),
            'us_active' => 1,
            'us_name' => 'Admin',
            'us_secondname' => 'Secondname',
            'us_lastname' => 'Lastname',
            'us_email' => 'devedumos@gmail.com',
            'us_regtime' => date('Y-m-d H:i:s'),
            'us_workposition' => 'Site admin auto created',
            'selectedGroups' => [1],

//            'auth_key' => '',
//            'us_xtime' => '',
//            'us_logintime' => '',
//            'us_checkwordtime' => '',
//            'us_password_hash' => '',
//            'us_chekword_hash' => '',
//            'email_confirm_token' => '',
//            'password_reset_token' => '',
        ];

        $model->setPassword($password);
/*
        $model->newPassword = $password;
        $model->generateAuthKey();
        $template = empty($template) ? 'user_create_info' : $template;
        $subject = empty($subject) ? ('Уведомление портала ' . Yii::$app->name) : $subject;

        $model->sendNotificate($template, $subject, ['model' => $model] );
*/
        if( !$model->save() ) {
            $s = 'Error save init admin: ' . print_r($model->getErrors());
            Yii::error($s);
            echo $s;
        }
    }
}
