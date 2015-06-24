<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Message */
?>
<div class="alert alert-success" role="alert">

    <h3><?= Html::encode($model->msg_pers_name . ' ' . $model->msg_pers_secname) ?>,</h3>
    <p>Спасибо за Вашу оценку деятельности наших сотрудников.</p>
    <?= nl2br(Html::encode($msg)) ?>
    <!-- p>Результаты проверки модератором и ответ должностного лица
        будут отправлены на Ваш Email
        <strong><?= Html::encode($model->msg_pers_email) ?></strong></p -->

</div>
