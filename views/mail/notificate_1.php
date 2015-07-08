<?php
/**
 * User: KozminVA
 * Date: 24.03.2015
 * Time: 11:00
 *
 * ans_notif_revis
 * шаблон уведомления ответчика о замечаниях
 *
 */

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Message */

$aLink = ['message/answer', 'id'=>$model->msg_id];

?>

<p>Здравствуйте, <?= Html::encode($model->employee->getShortName()) ?>.</p>

<p>Напоминаем Вам, что необходимо подготовить ответ на обращение №<?= Html::encode($model->msg_id) ?> .</p>

<p>Для просмотра обращения перейдите по ссылке: <?= Html::a(Url::to($aLink, true), Url::to($aLink, true)) ?></p>

<p>&nbsp;</p>
<p>&nbsp;</p>

<?= $this->render('mail_footer', [], $this->context) ?>
