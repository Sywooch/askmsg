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

?>

<p>Здравствуйте, <?= Html::encode($model->getShortName()) ?>!</p>

<p>Департамент образования города Москвы подготовил ответ на ваше обращение №<?= Html::encode($model->msg_id) ?>.</p>

<p>Для просмотра обращения перейдите по ссылке: <?= Html::a(Url::to($aLink, true), Url::to($aLink, true)) ?></p>

<p>С уважением, Департамент образования города Москвы</p>

<p>Сообщение сгенерировано автоматически, отвечать на него не нужно</p>

