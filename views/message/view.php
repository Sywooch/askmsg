<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\assets\ListdataAsset;

/* @var $this yii\web\View */
/* @var $model app\models\Message */

$this->title = 'Обращение № ' . $model->msg_id;
$this->params['breadcrumbs'][] = ['label' => 'Сообщения', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

ListdataAsset::register($this);

?>
<div class="message-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= ''
//        $this->render(
//            '_view01',
//            [
//                'model' => $model,
//            ]
//        )
 ?>

</div>
