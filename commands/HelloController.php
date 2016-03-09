<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Usergroup;
use Yii;
use yii\console\Controller;
use app\models\User;
use app\models\Message;

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
        else {
            $orole = new Usergroup();
            $orole->usgr_gid = 1;
            $orole->usgr_uid = $model->us_id;
            if( !$orole->save() ) {
                $s = 'Error save init admin: ' . print_r($model->getErrors());
            }
        }
    }

    public function actionGender() {
//        print_r(iconv_get_encoding());
//        return;
        $q = Message::find()->where('msg_id > 0');
        $sEnc = 'UTF-8';
        $a = [];
        $nCou = 300;
        /** @var Message $ob */
        foreach($q->each() As $ob) {
            $sG = $ob->tryGender();
            $s = $ob->getFullName() . ' ' . $sG . "\n";
            $sname = mb_strtolower($ob->msg_pers_name, $sEnc);

            if( isset($a[$sname]) ) {
                $a[$sname]['cou']++;
                $a[$sname]['f'] .= "\n" . $ob->getFullName();
//                echo 'Inc ' . iconv('UTF-8', 'CP866', $sname) . ' ' . $a[$sname]['cou'] . "\n";
            }
            else {
                $a[$sname] = ['cou' => 1, 'g' => $sG, 'name' => $sname, 'f' => $ob->getFullName()];
//                echo 'Add ' . iconv('UTF-8', 'CP866', $sname) . "\n";
            }
//            if( 'айсулуу' == $sname ) {
//                echo iconv('UTF-8', 'CP866', $s);
//            }
            // echo iconv('UTF-8', 'WINDOWS-1251', $s);
//            echo iconv('UTF-8', 'CP866', $s);
//            if( $nCou-- == 0 ) {
//                break;
//            }
        }
        usort($a, function($a, $b){ return strcmp(sprintf("%s-%05d", $a['g'], $a['cou']), sprintf("%s-%05d", $b['g'], $b['cou'])); });
//            $a['g'] > $b['g']) || ($a['cou'] > $b['cou']); });
        foreach($a As $v) {
            if( $v['cou'] != 2 || $v['g'] != 'м' ) {
                continue;
            }
            $s = $v['name'] . ' ' . $v['g'] . ' ' . $v['cou'] . ($v['cou'] == 2 && $v['g'] == 'м' ? $v['f'] : '') . "\n";
            echo iconv('UTF-8', 'CP866', $s);
        }
    }

    /**
     * run: yii hello/hashpassword password email
     * @param string $password
     * @param string $email
     */
    public function actionHashpassword($password='', $email='') {
        if( empty($password) ) {
            echo "Password doesn't hashed !!!!!\n\nUsage: yii hello/hashpassword password [email]\n\n";
            exit(0);
        }
        $sHash = Yii::$app->security->generatePasswordHash($password);
        $oUser = User::find()->where(['us_email' => $email])->one();
        if( empty($email) || ($oUser === null) ) {
            echo "Password [{$password}] hash: {$sHash}\n";
        }
        else {
            $sSql = 'Update ' . User::tableName() . ' Set us_password_hash = :hash Where us_email = :email Limit 1';
            $nUpd = Yii::$app
                ->db
                ->createCommand(
                    $sSql,
                    [
                        ':hash' => $sHash,
                        ':email' => $email,
                    ]
                )
                ->execute();
            echo 'Updated records: ' . $nUpd . ' for email ' . $email . " set password = {$password}\n";
        }
        exit(0);
    }
}
