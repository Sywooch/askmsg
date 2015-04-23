<?php
/**
 * User: KozminVA
 * Date: 26.02.2015
 * Time: 13:42
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/resetpassword', 'token' => $user->password_reset_token]);
?>
<div class="password-reset">
    <p>Здравствуйте, <?= Html::encode($user->us_name) ?>.</p>

    <p>Ниже ссылка для установки нового пароля:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>