<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Subjredirect */

$this->title = 'Добавление перенаправления';
$this->params['breadcrumbs'][] = ['label' => 'Перенаправления', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subjredirect-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
