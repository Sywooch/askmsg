<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Msgtags */

$this->title = 'Create Msgtags';
$this->params['breadcrumbs'][] = ['label' => 'Msgtags', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="msgtags-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
