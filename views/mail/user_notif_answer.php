<?php
/**
 * User: KozminVA
 * Date: 24.03.2015
 * Time: 11:00
 *
 * user_notif_show
 * шаблон уведомления пользователя при показе ответа на сообщение
 *
 */

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Message */

$aLink = ['message/view', 'id'=>$model->msg_id];

// $aMarkLink = ['message/mark', 'id'=>$model->msg_id];
$aMarkLink = $model->getMarkUrl();

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'mail_styles_data.php';

$bIsMediateAnswer = $model->hasMediateanswer() && $model->isMediateanswerFinished() && empty($model->msg_answer);

/* <?= $aMailTextStyles['large_text_01'] ?> */

?>

<p>Здравствуйте, <?= Html::encode($model->getShortName()) ?>.</p>

<p>Департамент образования города Москвы подготовил ответ на ваше обращение №<?= Html::encode($model->msg_id) ?>.</p>
<?php
if( $bIsMediateAnswer ) {
?>
    <p>Это промежуточный ответ. Окончательный ответ будет дан позднее.</p>
<?php
}
?>

<p>Для просмотра обращения перейдите по ссылке: <?= Html::a(Url::to($aLink, true), Url::to($aLink, true)) ?></p>

<?php
if( !$bIsMediateAnswer ) {
?>
    <p>Вы можете оценить этот ответ на сайте по ссылке <?= Html::a(Url::to($aMarkLink, true), Url::to($aMarkLink, true)) ?> :<br />
        если вы удовлетворены ответом, выберите <a href="<?= Url::to(array_merge($aMarkLink, ['mark'=>5]), true) ?>">Да</a>,<br />
        если не удовлетворены - <a href="<?= Url::to(array_merge($aMarkLink, ['mark'=>0]), true) ?>">Нет</a>.</p>

    <p>Для выставления оценки Вам понадобится проверочный код: <?= $model->getTestCode() ?> .</p>
<?php
}
?>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>С уважением, Департамент образования города Москвы</p>

<p>Сообщение сгенерировано автоматически, отвечать на него не нужно</p>

