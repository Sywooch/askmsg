<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Mediateanswer */

$this->title = $model->ma_id;
$this->params['breadcrumbs'][] = ['label' => 'Mediateanswers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mediateanswer-view">

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
            'ma_created',
            'ma_text:ntext',
            'ma_remark:ntext',
            'ma_msg_id',
        ],
    ]) ?>

</div>
