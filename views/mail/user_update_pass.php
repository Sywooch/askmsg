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

//use yii;
use yii\helpers\Html;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\models\User */

$aLink = ['site/login'];

?>

<p>Здравствуйте, <?= Html::encode($model->getShortName()) ?>!</p>

<p>На сайте <?= Html::encode(Yii::$app->name) ?> изменен Ваш пароль.</p>

<p>Для входа перейдите по ссылке: <?= Html::a(Url::to($aLink, true), Url::to($aLink, true)) ?></p>

<p>Данные для входа:</p>

<p>Логин: <?= Html::encode($model->us_login) ?></p>

<p>Пароль: <?= Html::encode($model->newPassword) ?></p>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>С уважением, Департамент образования города Москвы</p>

<p>Сообщение сгенерировано автоматически, отвечать на него не нужно</p>

