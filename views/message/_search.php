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
    $action = ['admin'];
}

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

    $aParam12 = [
        'horizontalCssClasses' => [
            'label' => 'col-sm-1',
            'offset' => 'col-sm-offset-1',
            'wrapper' => 'col-sm-11',
        ],
    ];

    ?>

    <div class="col-sm-4">
    <?= $form->field($model, 'msg_id') ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'msg_pers_email') ?>
    </div>

    <div class="col-sm-4">
        <?php
/*
            $sDateFld = DatePicker::widget([
                'model' => $model,
                'attribute' => 'msg_createtime',
                'language' => 'ru',
                //'dateFormat' => 'yyyy-MM-dd',
            ]);
*/
            echo $form
                ->field(
                    $model,
                    'msg_createtime'
                )
                ->widget(
                    DatePicker::className(),
                    [
                        'model' => $model,
                        'attribute' => 'msg_createtime',
                        'language' => 'ru',
                        'dateFormat' => 'dd.MM.yyyy',
                        'options' => ['class' => 'form-control',],
                    ]
                );
/*
            ->widget(DateControl::classname(), [
                'type'=>DateControl::FORMAT_DATE,
                'language' => 'ru',
                'name' => Html::getInputName($model, 'msg_createtime'),
//                'ajaxConversion'=>false,
                'options' => [
                    'pluginOptions' => [
                        'autoclose' => true
                    ]
                ]
            ])
/*        widget([
                'class' => DateControl::className(),
                'language' => 'ru',
                'type'=>DateControl::FORMAT_DATE,
//                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose'=>true,
    //                'format' => 'dd-M-yyyy'
                ]
            ]) */
        ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'msg_pers_lastname') ?>
    </div>

    <div class="col-sm-4">
    <?php
    $aAnsw = User::getGroupUsers(Rolesimport::ROLE_ANSWER_DOGM, '', '{{val}}');
    ?>

    <?= $form
        ->field($model, 'msg_empl_id')
        ->widget(Select2::classname(), [
            'data' => $aAnsw,
            'language' => 'ru',
            'options' => ['placeholder' => 'Выберите из списка ...'],
            'pluginOptions' => [
                'allowClear' => true,
            ],
            'pluginEvents' => [
//                        'change' => 'function(event) { jQuery("#'.Html::getInputId($model, 'msg_empl_id').'").val(event.val); console.log("change", event); }',
            ],
        ]);
    ?>
    </div>

    <div class="col-sm-4">

        <?= $form->field($model, 'msg_pers_org') ?>
<?php
/*
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
                                console.log("results("+page+") data = ", data);
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
                    var sIdReg = "'.Html::getInputId($model, 'msg_pers_region').'";
                    jQuery("#'.Html::getInputId($model, 'msg_pers_org').'").val(event.added.text);
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
*/
?>

    </div>

    <div class="col-sm-12">
    <?= $form
        ->field(
            $model,
            'msg_subject', $aParam12)
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
    <?= $form
        ->field($model, 'alltags', $aParam12)
        ->widget(Select2::classname(), [
            'data' => ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_TAG), 'tag_id', 'tag_title'),
            'language' => 'ru',
            'options' => [
                'multiple' => true,
//                        'tags' => true,
                'placeholder' => 'Выберите теги ...',
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]) ?>

    </div>

    <div class="col-sm-12">
        <?= $form
            ->field($model, 'msg_flag', $aParam12)
//            ->field($model, '_flagsstring')
            ->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Msgflags::find()->asArray()->all(), 'fl_id', 'fl_sname' ),
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Выберите состояние ...',
                    'multiple' => true,
                ],
                'pluginOptions' => [
//                    'tags' => array_values(ArrayHelper::map(Msgflags::getStateData(), 'fl_id', 'fl_sname' )),
                    'allowClear' => true,
                ],
            ]);
        ?>
    </div>

    <?php /* echo $form->field($model, 'msg_active') ?>
    <?php // echo $form->field($model, 'msg_pers_secname') ?>
    <?php // echo $form->field($model, 'msg_pers_name') ?>
    <?php // echo $form->field($model, 'msg_pers_email') ?>
    <?php // echo $form->field($model, 'msg_pers_phone') ?>
    <?php // echo $form->field($model, 'msg_pers_org') ?>
    <?php // echo $form->field($model, 'msg_pers_region') ?>
    <?php // echo $form->field($model, 'msg_pers_text') ?>
    <?php // echo $form->field($model, 'msg_comment') ?>
    <?php // echo $form->field($model, 'msg_empl_command') ?>
    <?php // echo $form->field($model, 'msg_empl_remark') ?>
    <?php // echo $form->field($model, 'msg_answer') ?>
    <?php // echo $form->field($model, 'msg_answertime') ?>
    <?php // echo $form->field($model, 'msg_oldcomment') */ ?>


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

//    <div class="clearfix"></div>
    ?>
</div>

<div class="clearfix"></div>
