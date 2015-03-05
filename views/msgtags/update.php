<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Msgtags */

$this->title = 'Update Msgtags: ' . ' ' . $model->mt_id;
$this->params['breadcrumbs'][] = ['label' => 'Msgtags', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->mt_id, 'url' => ['view', 'id' => $model->mt_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="msgtags-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
