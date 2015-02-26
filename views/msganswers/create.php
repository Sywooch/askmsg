<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Msganswers */

$this->title = 'Create Msganswers';
$this->params['breadcrumbs'][] = ['label' => 'Msganswers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="msganswers-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
