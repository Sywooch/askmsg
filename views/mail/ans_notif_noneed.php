<?php
/**
 * User: KozminVA
 * Date: 24.03.2015
 * Time: 11:00
 *
 * ans_notif_instr
 * шаблон уведомления ответчика о новом поручении
 *
 */

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Message */

$aLink = ['message/answer', 'id'=>$model->msg_id];

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'mail_styles_data.php';

?>

<p>Здравствуйте, <?= Html::encode($user->getShortName()) ?>.</p>

<p>Поручение №<?= Html::encode($model->msg_id) ?> снято с Вас как с исполнителя.</p>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>С уважением, Департамент образования города Москвы.</p>

<p>Сообщение сгенерировано автоматически, отвечать на него не нужно.</p>
