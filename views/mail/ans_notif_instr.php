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

<p>Здравствуйте, <?= Html::encode($model->employee->getShortName()) ?>!</p>

<p>Поступило новое поручение №<?= Html::encode($model->msg_id) ?>.</p>

<p<?= $aMailTextStyles['large_text_01'] ?>>Текст поручения:</p>
<p><?= Html::encode($model->msg_empl_command) ?></p>

<p<?= $aMailTextStyles['large_text_01'] ?>><b>Автор обращения: </b></p>
<p><?= Html::encode($model->getFullName()) . ', ' . Html::encode($model->msg_pers_email) . ', ' . Html::encode($model->msg_pers_phone) ?></p>

<p<?= $aMailTextStyles['large_text_01'] ?>><b>Сообщение: </b></p>
<p><?= Html::encode($model->msg_oldcomment) ?></p>
<p><?= ($model->subject ? Html::encode($model->subject->tag_title) : '') ?></p>
<p><?= Html::encode($model->msg_pers_text) ?></p>

<?php

if( !empty($model->answers) ) {
    $a = User::find()->where(['us_id' => array_slice($model->getAllanswers(), 1)])->all();
    if( count($a) > 0 ):
    ?>
<p<?= $aMailTextStyles['large_text_01'] ?>>Соисполнители:</p>
    <?php
    endif;
    foreach($a As $ob) { ?>
<p><?= Html::encode($ob->getFullName()) ?>, <?= Html::encode($ob->us_workposition) ?>, <?= Html::encode($ob->us_email) ?></p>
<p> </p>
    <?php
    }
}
?>

<p>&nbsp;</p>
<p<?= $aMailTextStyles['large_text_01'] ?>>Для подготовки ответа пройдите по ссылке:</p>
<p><?= Html::a(Url::to($aLink, true), Url::to($aLink, true)) ?></p>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>С уважением, Департамент образования города Москвы.</p>

<p>Сообщение сгенерировано автоматически, отвечать на него не нужно.</p>
