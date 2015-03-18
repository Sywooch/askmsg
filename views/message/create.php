<?php

use yii\helpers\Html;
use app\models\Rolesimport;


/* @var $this yii\web\View */
/* @var $model app\models\Message */
$aData = $model->getScenariosData();
$url = ['list'];
if( !$model->isNewRecord ) {
    $isModerate = \Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM);
    $isAnswer = \Yii::$app->user->can(Rolesimport::ROLE_ANSWER_DOGM);

    if( $isAnswer ) {
        $url = ['answerlist'];
    }

    if( $isModerate ) {
        $url = ['moderatelist'];
    }
}

$this->title = $aData['title'] . ($model->isNewRecord ? '' : (' № ' . $model->msg_id));
$this->params['breadcrumbs'] = [];
// $this->params['breadcrumbs'][] = ['label' => 'Обращения', 'url' => $url];
// $this->params['breadcrumbs'][] = $this->title;

/*
<h1><?= Html::encode($this->title) ?></h1>
*/

?>

<div class="message-create">

    <?= $this->render($aData['form'], [
        'model' => $model,
    ]) ?>

</div>
