<?php
/**
 * User: KozminVA
 * Date: 24.03.2015
 * Time: 11:00
 *
 * ans_notif_revis
 * шаблон уведомления ответчика о замечаниях
 *
 */

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Message */

$aLink = ['message/answer', 'id'=>$model->msg_id];

?>

<p>Здравствуйте, <?= Html::encode($model->employee->getShortName()) ?>.</p>

<p>Ваш ответ на поручение №<?= Html::encode($model->msg_id) ?> был рассмотрен Департаментом образования города Москвы.</p>

<p>Необходимо внести изменения: <?= empty($model->msg_empl_remark) && $model->hasMediateanswer() ? Html::encode($model->mediateanswer->ma_remark) : Html::encode($model->msg_empl_remark) ?></p>

<p>Для просмотра обращения перейдите по ссылке: <?= Html::a(Url::to($aLink, true), Url::to($aLink, true)) ?></p>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>С уважением, Департамент образования города Москвы.</p>

<p>Сообщение сгенерировано автоматически, отвечать на него не нужно.</p>
