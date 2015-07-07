<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Notificateact */

$this->title = 'Список уведомлений';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="notificateact-update" style="margin-bottom: 36px;">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('actlist', []) ?>
</div>
