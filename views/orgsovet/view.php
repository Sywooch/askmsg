<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Orgsovet */

$this->title = $model->orgsov_id;
$this->params['breadcrumbs'][] = ['label' => 'Orgsovets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orgsovet-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->orgsov_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->orgsov_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'orgsov_id',
            'orgsov_sovet_id',
            'orgsov_ekis_id',
        ],
    ]) ?>

</div>
