<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sovet */

$this->title = 'Update Sovet: ' . ' ' . $model->sovet_id;
$this->params['breadcrumbs'][] = ['label' => 'Sovets', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->sovet_id, 'url' => ['view', 'id' => $model->sovet_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sovet-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
