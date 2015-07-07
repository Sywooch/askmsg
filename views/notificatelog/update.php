<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Notificatelog */

$this->title = 'Update Notificatelog: ' . ' ' . $model->ntflg_id;
$this->params['breadcrumbs'][] = ['label' => 'Notificatelogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ntflg_id, 'url' => ['view', 'id' => $model->ntflg_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="notificatelog-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
