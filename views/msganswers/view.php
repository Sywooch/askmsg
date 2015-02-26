<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Msganswers */

$this->title = $model->ma_id;
$this->params['breadcrumbs'][] = ['label' => 'Msganswers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="msganswers-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ma_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ma_id], [
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
            'ma_id',
            'ma_message_id',
            'ma_user_id',
        ],
    ]) ?>

</div>
