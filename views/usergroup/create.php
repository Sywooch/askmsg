<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Usergroup */

$this->title = 'Create Usergroup';
$this->params['breadcrumbs'][] = ['label' => 'Usergroups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usergroup-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
