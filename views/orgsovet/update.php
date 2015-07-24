<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Orgsovet */

$this->title = 'Update Orgsovet: ' . ' ' . $model->orgsov_id;
$this->params['breadcrumbs'][] = ['label' => 'Orgsovets', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->orgsov_id, 'url' => ['view', 'id' => $model->orgsov_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="orgsovet-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
