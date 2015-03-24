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

?>

<p>Здравствуйте, <?= Html::encode($model->employee->getShortName()) ?>!</p>

<p>Поступило новое поручение №<?= Html::encode($model->msg_id) ?>.</p>
<?php

if( !empty($model->answers) ) {
    $a = User::find()->where(['us_id' => array_slice($model->getAllanswers(), 1)])->all();
    if( count($a) > 0 ):
    ?>
<p>Соисполнители:</p>
    <?php
    endif;
    foreach($a As $ob) { ?>
<p><?= Html::encode($ob->getFullName()) ?></p>
<p><?= Html::encode($ob->us_workposition) ?></p>
<p><?= Html::encode($ob->us_email) ?></p>
<p> </p>
    <?php
    }
}
?>

<p>Для подготовки ответа пройдите по ссылке: <?= Html::a(Url::to($aLink, true), Url::to($aLink, true)) ?></p>

<p>С уважением, Департамент образования города Москвы.</p>

<p>Сообщение сгенерировано автоматически, отвечать на него не нужно.</p>
