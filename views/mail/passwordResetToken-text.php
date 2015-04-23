<?php
/**
 * User: KozminVA
 * Date: 26.02.2015
 * Time: 13:43
 */

/* @var $this yii\web\View */
/* @var $user app\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/resetpassword', 'token' => $user->password_reset_token]);
?>
Здравствуйте, <?= $user->us_name ?>.

Ниже ссылка для установки нового пароля:

<?= $resetLink ?>