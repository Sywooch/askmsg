<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\web\View;
use app\assets\ListdataAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AppealSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->name; // 'Обращения граждан';
$this->params['breadcrumbs'][] = $this->title;

ListdataAsset::register($this);

?>
<div class="appeal-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="col-sm-12 no-horisontal-padding">
        <div class="form-group">
            <?= Html::a('Скрыть', '#', ['class' => 'btn btn-default pull-right no-horisontal-margin', 'id'=>'hidesearchpanel', 'role'=>"button"]) ?>
            <div class="clearfix"></div>
        </div>
    </div>

    <?php
    $idserchblock = 'idsearchpanel';
    echo $this->render(
        '_search-public',
        [
            'model' => $searchModel,
            'action' => ['index'],
            'idserchblock' => $idserchblock,
        ]
    );

    // показ/скрытие формы фильтрации
    $sJs =  <<<EOT
var oPanel = jQuery("#{$idserchblock}"),
    oLink = jQuery("#hidesearchpanel"),
    renameButton = function() {
        oLink.text((oPanel.is(":visible") ? "Скрыть" : "Показать") + " форму поиска");
    },
    toggleSearchPanel = function() {
        oPanel.toggle();
        renameButton();
    };

renameButton();
oLink.on(
    "click",
    function(event){ event.preventDefault(); toggleSearchPanel(); return false; }
);

EOT;
    $this->registerJs($sJs, View::POS_READY , 'togglesearchpanel');

    ?>


    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_view-public',
        'layout' => "{items}\n{pager}",
    ]); ?>

</div>
