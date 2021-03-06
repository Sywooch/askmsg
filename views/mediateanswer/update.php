<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Mediateanswer */
/* @var $message app\models\Message */

$this->title = 'Промежуточный ответ' . ' на обращение ' . $message->msg_id;
$this->params['breadcrumbs'] = [];

//$this->params['breadcrumbs'][] = ['label' => 'Mediateanswers', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->ma_id, 'url' => ['view', 'id' => $model->ma_id]];
//$this->params['breadcrumbs'][] = 'Update';
/* <h1><?= Html::encode($this->title) ?></h1> */

?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="mediateanswer-update">
    <?= $this->render('_form-answer', [
        'model' => $model,
        'message' => $message,
    ]) ?>
</div>
