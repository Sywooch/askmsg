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
use app\models\Msgflags;

/* @var $this yii\web\View */
/* @var $model app\models\Message */

$bSoglas = in_array($model->msg_flag, [Msgflags::MFLG_SHOW_NOSOGL, Msgflags::MFLG_INT_NOSOGL, ]);
$sOp = $bSoglas ? 'согласовать' : 'подготовить';
$sAct = $bSoglas ? 'согласования' : 'просмотра';

$aLink = ['message/'. ($bSoglas ? 'curatortest' : 'view'), 'id'=>$model->msg_id];

?>

<p>Здравствуйте, <?= Html::encode($model->curator->getShortName()) ?>.</p>

<p>Напоминаем Вам, что необходимо <?= $sOp ?> ответ на обращение №<?= Html::encode($model->msg_id) ?>.</p>

<p>Ответчиком на это обращение является <?= Html::encode($model->employee->getFullName()) ?>.</p>

<p>Для <?= $sAct ?> обращения перейдите по ссылке: <?= Html::a(Url::to($aLink, true), Url::to($aLink, true)) ?></p>

<p>&nbsp;</p>
<p>&nbsp;</p>

<?= $this->render('mail_footer', [], $this->context) ?>
