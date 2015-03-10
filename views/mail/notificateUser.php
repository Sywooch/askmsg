<?php
/**
 * User: KozminVA
 * Date: 10.03.2015
 * Time: 16:20
 */

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Message */

$aLink = ['message/view', 'id'=>$model->msg_id];

?>

<p>Здравствуйте, <?= Html::encode($model->getShortName()) ?>!</p>

<p>Ваше обращение в проекте <?= Html::encode(Yii::$app->name) ?> №<?= Html::encode($model->msg_id) ?> прошло обработку.</p>

<p>Текущий статус обращения: <?= Html::encode(preg_replace('|\\[[^\\]]+\\]|', '', $model->flag->fl_name)) ?></p>

<p>Посмотреть обращение Вы можете по ссылке: <?= Html::a(Url::to($aLink, true), $aLink) ?></p>


