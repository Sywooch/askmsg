<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Msgflags */

$this->title = 'Create Msgflags';
$this->params['breadcrumbs'][] = ['label' => 'Msgflags', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="msgflags-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
