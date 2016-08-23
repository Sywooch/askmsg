<?php
/**
 * User: KozminVA
 * Date: 24.03.2015
 * Time: 11:00
 *
 * user_notif_show
 * шаблон уведомления пользователя при показе сообщения на сайте
 *
 */

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Message */

//$aLink = ['message/view', 'id'=>$model->msg_id];

?>

<p>Здравствуйте, <?= Html::encode($model->getShortName()) ?>.</p>

<p>Ваше обращение №<?= Html::encode($model->msg_id) ?> прошло модерацию на сайте Департамента образования города Москвы.</p>

<p>Исполнителем по Вашему обращению назначен
    <?= Html::encode($model->employee->getFullName()) ?> (<?= Html::encode($model->employee->us_workposition) ?>)
</p>

<p>Исполнителю дано поручение:
    <?= Html::encode($model->msg_empl_command) ?>
</p>


<p>&nbsp;</p>
<p>&nbsp;</p>
<p>С уважением, Департамент образования города Москвы</p>

<p>Сообщение сгенерировано автоматически, отвечать на него не нужно</p>

