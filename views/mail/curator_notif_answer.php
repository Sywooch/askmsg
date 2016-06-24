<?php
/**
 * User: KozminVA
 * Date: 24.03.2015
 * Time: 11:00
 *
 * ans_notif_instr
 * шаблон уведомления соответчика о новом поручении
 *
 */

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;
use app\models\Msgflags;

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'mail_styles_data.php';

/* @var $this yii\web\View */
/* @var $model app\models\Message */

$aLink = ['message/answer', 'id'=>$model->msg_id];
$aLinkCurator = ['message/curatortest', 'id'=>$model->msg_id]
?>

<p>Здравствуйте, <?= Html::encode($model->curator->getShortName()) ?>.</p>

<p>На обращение №<?= Html::encode($model->msg_id) ?> подготовлен ответ.</p>

<p<?= $aMailTextStyles['large_text_01'] ?>><b>Автор обращения: </b></p>
<p><?= Html::encode($model->getFullName()) . ', ' . Html::encode($model->msg_pers_email) . ', ' . Html::encode($model->msg_pers_phone) ?></p>

<p<?= $aMailTextStyles['large_text_01'] ?>><b>Обращение: </b></p>
<p><?= Html::encode($model->msg_oldcomment) ?></p>
<p><?= ($model->subject ? Html::encode($model->subject->tag_title) : '') ?></p>
<p><?= $model->msg_pers_text // Html::encode($model->msg_pers_text) ?></p>

<p<?= $aMailTextStyles['large_text_01'] ?>>Текст поручения:</p>
<p><?= Html::encode($model->msg_empl_command) ?></p>

<p<?= $aMailTextStyles['large_text_01'] ?>><b>Ответственный за публикацию ответа:</b></p>
<p><?= Html::encode($model->employee->getFullName()) ?>, <?= Html::encode($model->employee->us_workposition) ?>, <?= Html::encode($model->employee->us_email) ?></p>

<p<?= $aMailTextStyles['large_text_01'] ?>>Текст ответа:</p>
<p><?= empty($model->msg_answer) && $model->hasMediateanswer() ? $model->mediateanswer->ma_text : $model->msg_answer ?></p>

<?php
if( count($allusers) > 1 ) {
    ?>
    <p<?= $aMailTextStyles['large_text_01'] ?>>Для выполнения поручения назначены соисполнители:</p>

    <?php
    foreach($allusers As $ob) {
    ?>
<p><?= Html::encode($ob->getFullName()) ?>, <?= Html::encode($ob->us_workposition) ?>, <?= Html::encode($ob->us_email) ?></p>

    <?php
    }
}
?>


<p<?= $aMailTextStyles['large_text_01'] ?>><b>Информация Вам направлена для осуществления контроля исполнения данного поручения</b></p>

<?php
if( !in_array($model->msg_flag, [Msgflags::MFLG_INT_FIN_INSTR, Msgflags::MFLG_SHOW_ANSWER, ]) ):
?>

<p><b>Осуществить проверку сообщения необходимо по ссылке <?= Html::a(Url::to($aLinkCurator, true), Url::to($aLinkCurator, true)) ?></b></p>

<?php
endif;
?>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>С уважением, Департамент образования города Москвы.</p>

<p>Сообщение сгенерировано автоматически, отвечать на него не нужно.</p>
