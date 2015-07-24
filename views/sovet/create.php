<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Sovet */

$this->title = 'Create Sovet';
$this->params['breadcrumbs'][] = ['label' => 'Sovets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sovet-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
