<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Usergroup */

$this->title = 'Update Usergroup: ' . ' ' . $model->usgr_id;
$this->params['breadcrumbs'][] = ['label' => 'Usergroups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->usgr_id, 'url' => ['view', 'id' => $model->usgr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="usergroup-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
