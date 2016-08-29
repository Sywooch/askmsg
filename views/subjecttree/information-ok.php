<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SubjectTree */

$this->title = 'Спасибо за проявленный интерес';
//$this->params['breadcrumbs'][] = ['label' => 'Subject Trees', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-tree-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-success">
        <p>Спасибо за Ваш интерес к работе Департамента образования.</p>
        <p>Вы можете <?= Html::a('создать новое обращение к руководителю', '') ?>.</p>
    </div>


</div>
