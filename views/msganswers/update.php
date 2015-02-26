<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Msganswers */

$this->title = 'Update Msganswers: ' . ' ' . $model->ma_id;
$this->params['breadcrumbs'][] = ['label' => 'Msganswers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ma_id, 'url' => ['view', 'id' => $model->ma_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="msganswers-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
