<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Tags */

$this->title = 'Изменение тега ' . ' ' . $model->tag_title;
$this->params['breadcrumbs'][] = ['label' => 'Теги', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->tag_title, 'url' => ['view', 'id' => $model->tag_id]];
$this->params['breadcrumbs'][] = 'Изменение';
/* <h1><?= Html::encode($this->title) ?></h1> */

?>
<div class="tags-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
