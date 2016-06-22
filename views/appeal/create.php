<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Appeal */

//$this->title = 'Create Appeal';
//$this->params['breadcrumbs'][] = ['label' => 'Appeals', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;

$this->title = $model->isNewRecord ? 'Обращения к руководителю департамента' : ('Изменение обращения № ' . $model->msg_id);
$this->params['breadcrumbs'] = [];

if( $model->isNewRecord ) {
    echo $this->render('entry-text');
}

?>

<div class="appeal-create">

    <?= $this->render('_form_new', [
        'model' => $model,
    ]) ?>

</div>
