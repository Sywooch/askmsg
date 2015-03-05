<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Tags */

$this->title = 'Добавление тега';
$this->params['breadcrumbs'][] = ['label' => 'Теги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
/*     <h1><?= Html::encode($this->title) ?></h1> */
?>
<div class="tags-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
