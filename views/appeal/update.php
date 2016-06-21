<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Appeal */

$this->title = 'Update Appeal: ' . ' ' . $model->ap_id;
$this->params['breadcrumbs'][] = ['label' => 'Appeals', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ap_id, 'url' => ['view', 'id' => $model->ap_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="appeal-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
