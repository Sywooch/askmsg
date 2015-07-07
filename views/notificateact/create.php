<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Notificateact */

$this->title = 'Create Notificateact';
$this->params['breadcrumbs'][] = ['label' => 'Notificateacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notificateact-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
