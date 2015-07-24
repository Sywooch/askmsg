<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Orgsovet */

$this->title = 'Create Orgsovet';
$this->params['breadcrumbs'][] = ['label' => 'Orgsovets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orgsovet-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
