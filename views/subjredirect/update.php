<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Subjredirect */

$this->title = 'Изменение перенаправления'; //  . ' ' . $model->redir_id;
$this->params['breadcrumbs'][] = ['label' => 'Перенаправления', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->redir_id, 'url' => ['view', 'id' => $model->redir_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subjredirect-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
