<?php
/**
 * User: KozminVA
 * Date: 26.02.2015
 * Time: 12:29
 */

// use Yii;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\models\User */

$loginLink = \Yii::$app->urlManager->createAbsoluteUrl(['site/login']);
?>

<p>Здравствуйте, <?= Html::encode($user->us_name) ?>.</p>

<p>Вы зарегистрированы в проекте <?= Html::encode(Yii::$app->name) ?></p>

<p>Для начала работы перейдите по ссылке <?= $loginLink  ?> и введите следующие данные:
<div>Логин: <?= $user->us_login ?></div>
<div>Пароль: <?= $password ?></div>

</p>