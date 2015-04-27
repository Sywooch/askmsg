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

/* <?= Html::encode(Yii::$app->name) ?> */
// , а также ознакомительную презентацию – шпаргалку.
// http://ask.educom.ru/respondent/index.php
?>

<p>Здравствуйте, <?= Html::encode($model->getShortName()) ?>.</p>

<p>По поручению руководителя, в системе «Обращения граждан» на сайте Департамента образования города Москвы
   для Вас создан личный кабинет, высылаю ссылку на систему, логин и пароль для входа.</p>

<p>Для входа перейдите по ссылке: <?= Html::a(Url::to($aLink, true), Url::to($aLink, true)) ?></p>

<p>Данные для входа:</p>

<p>Логин: <?= Html::encode($model->us_login) ?></p>
<p>Пароль: <?= Html::encode($model->newPassword) ?></p>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>Если у вас появятся вопросы относительно работы в системе, пожалуйста, обращайтесь.</p>
<p>Арутюнова Анна Михайловна (8-495-957-75-00, добавочный 39-056).</p>

