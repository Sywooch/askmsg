<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Msgflags */

$this->title = 'Update Msgflags: ' . ' ' . $model->fl_id;
$this->params['breadcrumbs'][] = ['label' => 'Msgflags', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fl_id, 'url' => ['view', 'id' => $model->fl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="msgflags-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
