<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Notificateact */

$this->title = 'Update Notificateact: ' . ' ' . $model->ntfd_id;
$this->params['breadcrumbs'][] = ['label' => 'Notificateacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ntfd_id, 'url' => ['view', 'id' => $model->ntfd_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="notificateact-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
