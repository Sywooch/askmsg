<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SubjectTree */

$this->title = 'Update Subject Tree: ' . ' ' . $model->subj_id;
$this->params['breadcrumbs'][] = ['label' => 'Subject Trees', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->subj_id, 'url' => ['view', 'id' => $model->subj_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="subject-tree-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
