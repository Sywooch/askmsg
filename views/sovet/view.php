<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Sovet */

$this->title = $model->sovet_id;
$this->params['breadcrumbs'][] = ['label' => 'Sovets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sovet-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->sovet_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->sovet_id], [
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
            'sovet_id',
            'sovet_title',
        ],
    ]) ?>

</div>
