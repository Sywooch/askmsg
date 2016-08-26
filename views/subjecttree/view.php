<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SubjectTree */
/* @var $child array */

$this->title = $model->subj_id;
$this->params['breadcrumbs'][] = ['label' => 'Subject Trees', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-tree-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->subj_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->subj_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'subj_id',
            'subj_created',
            'subj_variant:ntext',
            'subj_info:ntext',
            'subj_final_question:ntext',
            'subj_final_person:ntext',
            'subj_lft',
            'subj_rgt',
            'subj_level',
            'subj_parent_id',
        ],
    ]) ?>

</div>
