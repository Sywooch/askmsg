<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Mediateanswer */

$this->title = 'Create Mediateanswer';
$this->params['breadcrumbs'][] = ['label' => 'Mediateanswers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mediateanswer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
