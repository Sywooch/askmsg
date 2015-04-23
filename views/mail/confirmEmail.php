<?php
/**
 * User: KozminVA
 * Date: 26.02.2015
 * Time: 12:29
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\models\User */

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['site/confirmemail', 'token' => $user->email_confirm_token]);
?>

Здравствуйте, <?= Html::encode($user->us_name) ?>.

Для подтверждения адреса пройдите по ссылке:

<?= Html::a(Html::encode($confirmLink), $confirmLink) ?>

Если Вы не регистрировались у на нашем сайте, то просто удалите это письмо.