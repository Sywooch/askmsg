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

/* @var $this yii\web\View */
/* @var $model app\models\Message */

$aLink = ['message/answer', 'id'=>$model->msg_id];

?>

<p>Здравствуйте, <?= Html::encode($user->getShortName()) ?>!</p>

<p>Поступило новое поручение №<?= Html::encode($model->msg_id) ?>.</p>

<p>Текст поручения: </p>
<p><?= Html::encode($model->msg_empl_command) ?></p>

<p>Пользователь: </p>
<p><?= Html::encode($model->getFullName()) . ' ' . Html::encode($model->msg_pers_email) . ' ' . Html::encode($model->msg_pers_phone) ?></p>

<p>Сообщение: </p>
<p><?= Html::encode($model->msg_oldcomment) ?></p>
<p><?= ($model->subject ? Html::encode($model->subject->tag_title) : '') ?></p>
<p><?= Html::encode($model->msg_pers_text) ?></p>

<?php
if( count($allusers) > 1 ) {
    ?>
    <p>Для выполнения поручения необходимо связаться с соисполнителями и подготовить согласованный ответ.</p>

    <p>Соисполнители:</p>
    <?php
    foreach($allusers As $ob) {
        if( $ob->us_id == $user->us_id ) {
            continue;
        }
        ?>
<p><?= Html::encode($ob->getFullName()) ?> <?= Html::encode($ob->us_workposition) ?> <?= Html::encode($ob->us_email) ?></p>

    <?php
    }
}
?>

<p>Ответственный за публикацию ответа: <?= Html::encode($mainuser->getFullName()) ?> <?= Html::encode($mainuser->us_workposition) ?> <?= Html::encode($mainuser->us_email) ?></p>

<p>С уважением, Департамент образования города Москвы.</p>

<p>Сообщение сгенерировано автоматически, отвечать на него не нужно.</p>
