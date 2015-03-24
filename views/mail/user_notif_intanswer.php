<?php
/**
 * User: KozminVA
 * Date: 24.03.2015
 * Time: 11:00
 *
 * user_notif_show
 * шаблон уведомления пользователя при ответе на скрытое поручение
 *
 */

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Message */

$aLink = ['message/view', 'id'=>$model->msg_id];

?>

<p>Здравствуйте, <?= Html::encode($model->getShortName()) ?>!</p>

<p>Ваше обращение №<?= Html::encode($model->msg_id) ?> было рассмотрено Департаментом образования города Москвы.</p>

<p><strong>Ответ:</strong></p>
<?= $model->msg_answer ?>

<p><strong>Ответчик:</strong></p>
<p><?= $model->employee->getFullName() ?></p>
<p><?= $model->employee->us_workposition ?></p>

<p>С уважением, Департамент образования города Москвы</p>

<p>Сообщение сгенерировано автоматически, отвечать на него не нужно</p>



