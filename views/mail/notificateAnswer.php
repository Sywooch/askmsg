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
$aListLink = ['message/answerlist'];

?>

<p>Здравствуйте, <?= Html::encode($user['shortname']) ?>.</p>

<p>Это извещение проекта <?= Html::encode(Yii::$app->name) ?> о сообщении №<?= Html::encode($model->msg_id) ?>.</p>

<p>Текущий статус обращения: <?= Html::encode(preg_replace('|\\[[^\\]]+\\]|', '', $model->flag->fl_name)) ?></p>

<p>Поручение к сообщению: <?= Html::encode($model->msg_empl_command) ?></p>
<?php
if( strlen($model->msg_empl_remark) > 0 ) {
?>

    <p>Замечание к сообщению: <?= Html::encode($model->msg_empl_remark) ?></p>
<?php
}
?>

<p>Посмотреть обращение Вы можете по ссылке: <?= Html::a(Url::to($aLink, true), Url::to($aLink, true)) ?></p>

<p>Все назначенные Вам обращения доступны по ссылке: <?= Html::a(Url::to($aListLink, true), Url::to($aListLink, true)) ?></p>

