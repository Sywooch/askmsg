<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Message */
$aData = $model->getScenariosData();
$this->title = $aData['title'] . ($model->isNewRecord ? '' : (' № ' . $model->msg_id));
$this->params['breadcrumbs'][] = ['label' => 'Сообщения', 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="message-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render($aData['form'], [
        'model' => $model,
    ]) ?>

</div>
