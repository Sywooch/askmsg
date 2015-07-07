<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Notificateact */

$this->title = $model->ntfd_id;
$this->params['breadcrumbs'][] = ['label' => 'Notificateacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notificateact-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ntfd_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ntfd_id], [
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
            'ntfd_id',
            'ntfd_message_age',
            'ntfd_operate',
            'ntfd_flag',
        ],
    ]) ?>

</div>
