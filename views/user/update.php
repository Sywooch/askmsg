<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$sTitle = $model->us_lastname . ' ' .$model->us_name;
$this->title = 'Изменить пользователя: ' . ' ' . $sTitle;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $sTitle, 'url' => ['view', 'id' => $model->us_id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
