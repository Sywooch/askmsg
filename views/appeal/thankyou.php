<?php

use yii\helpers\Html;

$this->title = 'Обращения к руководителю департамента';
$this->params['breadcrumbs'] = [];


/* @var $this yii\web\View */
/* @var $model app\models\Message */
?>
<div class="alert alert-success" role="alert">

    <h3><?= Html::encode($model->ap_pers_name . ' ' . $model->ap_pers_secname) ?>,</h3>
    <p>Ваше обращение направлено в департамент образования и после
        рассмотрения модератором будет направлено на рассмотрение
        соответствующему должностному лицу.</p>
    <p>Результаты проверки модератором и ответ должностного лица
        будут отправлены на Ваш Email
        <strong><?= Html::encode($model->ap_pers_email) ?></strong></p>

</div>
