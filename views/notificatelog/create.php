<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Notificatelog */

$this->title = 'Create Notificatelog';
$this->params['breadcrumbs'][] = ['label' => 'Notificatelogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notificatelog-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
