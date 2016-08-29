<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SubjectTree */

$this->title = 'Ваше сообщение отправлено';
//$this->params['breadcrumbs'][] = ['label' => 'Subject Trees', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-tree-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-success">
        <p>Ваше сообщение отправлено модератору для проверки и назначения ответственного лица, которое ответит на Ваше обращение.</p>
    </div>


</div>
