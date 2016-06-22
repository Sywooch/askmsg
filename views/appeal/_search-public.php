<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use yii\jui\DatePicker;

use kartik\select2\Select2;
//use kartik\date\DatePicker;
use kartik\datecontrol\DateControl;

use app\models\Tags;
use app\models\Rolesimport;
use app\models\User;
use app\models\Msgflags;

/* @var $this yii\web\View */
/* @var $model app\models\MessageSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $idserchblock string */

if( !isset($action) ) {
    $action = ['index'];
}

$sCss = <<<EOT
.col-sm-1-dop {
/*    width: 8.33333%;*/
    width: 12.4999%;
}
.col-sm-11-dop {
/*    width: 91.6667%;*/
    width: 87.5%;
}
EOT;
$this->registerCss($sCss);

?>

<div class="message-search" id="<?= $idserchblock; ?>" style="<?= $model->isEmpty() ? 'display: none; ' : '' ?>clear: both; border: 1px solid #777777; border-radius: 4px; background-color: #eeeeee; padding-top: 2em; padding-bottom: 1em; margin-bottom: 2em;">

    <?php $form = ActiveForm::begin([
        'action' => $action,
        'method' => 'get',
        'id' => 'filter-message-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
//                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
                'label' => 'col-sm-3',
                'offset' => 'col-sm-offset-3',
                'wrapper' => 'col-sm-9',
//                    'error' => '',
//                    'hint' => '',
            ],
        ],
    ]);

    $aSubjectParam = [
        'horizontalCssClasses' => [
            'label' => 'col-sm-1 col-sm-1-dop',
            'offset' => 'col-sm-offset-1',
            'wrapper' => 'col-sm-11 col-sm-11-dop',
        ],
        'inputOptions' => [
            'disabled' => true,
        ]
    ];

    ?>

    <div class="col-sm-4">
    <?= $form->field($model, 'ap_id') ?>
    </div>

    <div class="col-sm-4">
        <?php
            echo $form
                ->field(
                    $model,
                    'ap_created'
                )
                ->widget(
                    DatePicker::className(),
                    [
                        'model' => $model,
                        'attribute' => 'ap_created',
                        'language' => 'ru',
                        'dateFormat' => 'dd.MM.yyyy',
                        'options' => ['class' => 'form-control',],
                    ]
                );
        ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'ap_pers_lastname') ?>
    </div>

    <div class="col-sm-4">
    <?= $form->field(
        $model,
        'ekis_id'
    )
        ->widget(Select2::classname(), [
            'language' => 'ru',
            'pluginOptions' => [
                'allowClear' => true,
                'initSelection' => new JsExpression('function (element, callback) {
                    if( element.val() > 0 ) {
                        $.ajax({
                            method: "POST",
                            url: "http://hastur.temocenter.ru/task/eo.search/",
                            dataType: "json",
                            data: {
                                filters: {
                                    eo_id: element.val(),
                                },
                                maskarade: {
                                    eo_id: "id",
                                    eo_short_name: "text"
                                },
                                fields: ["eo_id", "eo_short_name", "eo_district_name_id"].join(";")
                            },
                            success: function (data) {
                                callback(data.list.pop());
                            }
                        });
                    }
                }'),
                'ajax' =>[
                    'method' => 'POST',
                    'url' => "http://hastur.temocenter.ru/task/eo.search/forhost/ask.educom.ru",
                    'dataType' => 'json',
                    'withCredentials' => true,
                    'data' => new JsExpression('function (term, page) {
//                        console.log("data("+term+", "+page+")");
                        return {
                            filters: {eo_name: term, eo_short_name: term},
                            maskarade: {eo_id: "id", eo_short_name: "text", eo_district_name_id: "area_id", eo_subordination_name: "district"},
                            fields: "eo_id;eo_short_name;eo_subordination_name_id;eo_district_name_id",
                            limit: 10,
                            start: (page - 1) * 10,
                            "_": (new Date()).getSeconds()
                        };
                    }'),

                    'results' => new JsExpression('function (data, page) {
//                                console.log("results("+page+") data = ", data);
                                var more = (page * 10) < data.total; // whether or not there are more results available
                                return {results: data.list, more: more};
//                                return { results: data.list };
                             }'),
                    'id' => new JsExpression(
                        'function(item){return item.id;}'
                    ),
                ],
                'formatResult' => new JsExpression(
                    'function (item) {
                        return formatSelect(item, "text", "district");
                    }'
                ),
                'escapeMarkup' => new JsExpression('function (m) { return m; }'),
            ],

            'pluginEvents' => [
                'change' => 'function(event) {
                    var sIdReg = "'.Html::getInputId($model, 'ap_pers_region').'";
                    jQuery("#'.Html::getInputId($model, 'ap_pers_org').'").val(event.added.text);
                    jQuery("#"+sIdReg).val(event.added.area_id);
//                    console.log("change", event);
//                    console.log("set " + sIdReg + " = " + event.added.area_id);
                }',
            ],

            'options' => [
//                    'multiple' => true,
                'placeholder' => 'Выберите учреждение ...',
            ],
        ])
    ?>

    </div>

    <div class="col-sm-8">
    <?= $form
        ->field(
            $model,
            'ap_subject',
            $aSubjectParam)
        ->widget(Select2::classname(), [
            'data' => ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_SUBJECT), 'tag_id', 'tag_title'),
            'language' => 'ru',
            'options' => [
//                    'multiple' => true,
                'placeholder' => 'Выберите тему сообщения ...',
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]) ?>
    </div>


    <div class="col-sm-12">
        <!-- div class="form-group" -->
        <?= Html::a('Сбросить настройки', $action, ['class' => 'btn btn-default pull-right']) ?>
        <div class="pull-right" style="width: 2em;">&nbsp;</div>
        <?= Html::submitButton('Искать', ['class' => 'btn btn-success pull-right']) ?>
        <!-- /div -->
    </div>
    <div class="clearfix"></div>

    <?php ActiveForm::end();
    // функция форматирования результатов в список для select2
    $sJs =  <<<EOT
var formatSelect = function(item, text, description) {
    return  item[text] + "<span class=\\"description\\">" + item[description] + "</span>";
}

EOT;
    $this->registerJs($sJs, View::POS_END , 'showselectpart');

    ?>
</div>

<div class="clearfix"></div>
