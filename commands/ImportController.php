<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 23.07.2015
 * Time: 15:44
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Html;

use Httpful\Request;
use Httpful\Response;

use app\components\SwiftHeaders;

class ImportController extends Controller {

    /**
     *
     */
    public function actionIndex($message = 'hello world')
    {
        echo "actions: sovet - import council\n";
    }

    /**
     * Импорт советов директоров
     */
    public function actionSovet()
    {
        $_SERVER['HTTP_HOST'] = 'ask.educom.ru';
        $sPathSovet = '/api/dicts/council.json';
        $sPathOrg = '/api/eduoffices/sphinx.json?archive=0&area=0&district=0&metro=0&program=0&council=%d&type=0&class=0&by_order=0&by_legal=0&page=1&limit=10000';

        if( !$this->tableExists('{{%sovet}}') ) {
            echo "Need create table for 'sovet' - do migration\n";
            return;
        }

        $db = Yii::$app->db;

        $url = Yii::$app->params['import.host'] . $sPathSovet;
        $request = Request::get($url);
        $request->parse_callback = function($body) { return json_decode($body, true); };

        /** @var Response $response */
        $response = $request->send();

        $SovetId = [];
        if( count($response->body) > 0 ) {
            $aErrInSovet = [];
//        if( false ) {
            $db->createCommand('Delete From {{%sovet}} Where sovet_id > 0')->execute();
            $db->createCommand('Alter Table {{%sovet}} AUTO_INCREMENT = 1')->execute();
            $val = [];
            $aErrInSovet = [];
            foreach($response->body As $v) {
                if( $v['id'] < 1 ) {
                    continue;
                }

                $n = substr_count($v['name'], ',');
                if( $n != 1 ) {
                    $aErrInSovet[] = $v;
                }

//                echo print_r($v, true) . "\n";
                $val[] = '('.$v['id'].', '.$db->quoteValue($v['name']).')';
                $SovetId[] = $v['id'];
//                break;
            }
            if( count($aErrInSovet) > 0 ) {
                $sErr = '';
                foreach($aErrInSovet As $v) {
                    $sErr .= ($sErr == '' ? '' : "\n")
                            . $v['name'] . " [".$v['id']."]";
                }
                $sErr = "Мы ждем запятую в названии совета директоров:\n" . $sErr;
                echo str_repeat('*', 60) . "\n";
                echo '*' . str_pad('Error in council:', 58, ' ', STR_PAD_BOTH) . "*\n";
                echo str_repeat('*', 60) . "\n";
                echo $sErr . "\n";
                echo str_repeat('*', 60) . "\n";
                $subject = Yii::$app->name . ': неполадки с Советом директоров' ;
                $oMsg = Yii::$app->mailer->compose('@app/views/mail/notificate_support', ['html'=> nl2br(Html::encode($sErr)),])
                    ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                    ->setTo(isset(Yii::$app->params['notifyEmail']) ? Yii::$app->params['notifyEmail'] : Yii::$app->params['supportEmail'])
                    ->setSubject($subject);
                SwiftHeaders::setAntiSpamHeaders($oMsg, ['email' => Yii::$app->params['supportEmail']]);
                $oMsg->send();
            }
//            echo $val[0] . "\n";
            if( count($val) > 0 ) {
                $sql = 'Insert Into {{%sovet}} Values ' . implode(',', $val);
                echo $sql . "\n";
                $db->createCommand($sql)->execute();
            }
        }

        echo str_repeat('-', 30) . " finish sovet\n";

        $db->createCommand('Delete From {{%orgsovet}} Where orgsov_id > 0')->execute();
        $db->createCommand('Alter Table {{%orgsovet}} AUTO_INCREMENT = 1')->execute();

        foreach($SovetId As $id) {
            $url = Yii::$app->params['import.host'] . str_replace('%d', $id, $sPathOrg);
            $request = Request::get($url);
            $request->parse_callback = function($body) { return json_decode($body, true); };

            $response = $request->send();

            if( isset($response->body['total']) && ($response->body['total'] > 0) ) {
                $val = [];
                foreach($response->body['list'] As $v) {
                    $val[] = '('.$id.', '.$v['eo_id'].')';
                }
                if( count($val) > 0 ) {
                    $sql = 'Insert Into {{%orgsovet}} (orgsov_sovet_id, orgsov_ekis_id) Values ' . implode(',', $val);
                    echo $sql . "\n";
                    $db->createCommand($sql)->execute();
                }
            }

//            print_r($response->body);
            echo str_repeat('-', 30) . " finish sovet id = {$id} [" . $response->body['total'] . "]\n";

//            break;
        }


//        echo substr(print_r($response, true), 0, 500);
    }

    /**
     * Проверка наличия таблицы
     * @param string $name
     * @return bool
     */
    public function tableExists($name)
    {
        $name = Yii::$app->db->schema->getRawTableName($name);
        $a = Yii::$app->db->createCommand('Show tables Like :name', [':name' => $name])->queryOne();
        return $a === false ? $a : true;
    }

}