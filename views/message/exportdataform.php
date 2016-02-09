<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

use kartik\select2\Select2;
use kartik\widgets\DatePicker;

use yii\widgets\MaskedInput;

use app\models\Rolesimport;
use app\models\Tags;
use app\models\User;


/* @var $this yii\web\View */
/* @var $model app\models\ExportdataForm */

$this->title = 'Выгрузка сообщений';
$this->params['breadcrumbs'] = [];


// ***************************************************** Fields config:

$aAnsw = User::getGroupUsers(Rolesimport::ROLE_ANSWER_DOGM, ['us_active' => User::STATUS_ACTIVE], "{{val}}\n{{pos}}");

$aFieldlist = [
    'language' => 'ru',
    'data' => $model->prepareFieldNames(),
    'options' => [
        'multiple' => true,
        'placeholder' => 'Выберите поля для вывода ...',
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
];

$aSubject = [
    'language' => 'ru',
    'data' => ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_SUBJECT), 'tag_id', 'tag_title'),
//                'disabled' => $isModerate,
//                'readonly' => $isModerate,
    'options' => [
        'multiple' => true,
        'placeholder' => 'Выберите тему обращения ...',
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
];

$aTags = [
    'language' => 'ru',
    'data' => ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_TAG), 'tag_id', 'tag_title'),
    'options' => [
        'multiple' => true,
        'placeholder' => 'Выберите теги ...',
    ],
    'pluginOptions' => [
//        'tags' => array_values(ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_TAG), 'tag_id', 'tag_title')),
        'allowClear' => true,
    ],
];

$aAnswerUser = [
    'data' => $aAnsw,
    'language' => 'ru',
    'options' => [
        'multiple' => true,
        'placeholder' => 'Выберите из списка ...',
    ],
    'pluginOptions' => [
        'allowClear' => true,
        'formatResult' => new JsExpression('function(object, container, query, escapeMarkup){
                    var markup=[], aLines = object.text.split("\\n");
                    window.Select2.util.markMatch(aLines[0], query.term, markup, escapeMarkup);
                    return markup.join("") + "\\n<span class=\\"description\\">"+escapeMarkup(aLines[1])+"</span>";
            }'),
        'formatSelection' => new JsExpression('function (data, container, escapeMarkup) {
                var aLines = data ? data.text.split("\\n") : [""];
                return data ? escapeMarkup(aLines[0]) : undefined;
            }'),
    ],
//    'pluginEvents' => [
//        'change' => 'function(event) {
//                            // jQuery("#'.$sEmploeeId.'").val(event.val);
//                            console.log("change", event);
//                            console.log("New val = " + jQuery("#'.$sEmploeeId.'").val());
//                            filterButtons();
//                        }',
////                        'select2-selecting' => 'function(event) { console.log("select2-selecting", event); }',
//    ],
];

$aEkisid = [
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
                return  item["text"] + "<span class=\\"description\\">" + item["district"] + "</span>";
            }'
        ),
        'escapeMarkup' => new JsExpression('function (m) { return m; }'),
    ],

//    'pluginEvents' => [
//        'change' => 'function(event) {
//                    var sIdReg = "'.Html::getInputId($model, 'msg_pers_region').'";
//                    jQuery("#'.Html::getInputId($model, 'msg_pers_org').'").val(event.added.text);
//                    jQuery("#"+sIdReg).val(event.added.area_id);
////                    console.log("change", event);
////                    console.log("set " + sIdReg + " = " + event.added.area_id);
//                }',
//    ],

    'options' => [
        'multiple' => true,
        'placeholder' => 'Выберите учреждение ...',
    ],
]


?>
<h2><?= Html::encode($this->title) ?></h2>
<div class="message-exportdataform">
    <?php $form = ActiveForm::begin([
        'id' => 'exportdata-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validateOnType' => false,
        'options'=>[
            'enctype'=>'multipart/form-data'
        ],
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{hint}\n{error}",
        ],
    ]);

    ?>

    <div class="col-sm-4">
        <?= $form->field($model, 'msg_pers_lastname')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'msg_pers_name')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'msg_pers_secname')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="clearfix"></div>

    <div class="col-sm-4">
        <?= $form->field($model, 'msg_pers_email')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'msg_pers_phone')->widget(MaskedInput::className(),[
            'name' => 'msg_pers_phone',
            'mask' => '+7(999) 999-99-99'
        ]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form
            ->field($model, 'ekis_id')
            ->widget(Select2::classname(), $aEkisid)
        ?>
    </div>

    <div class="clearfix"></div>

    <div class="col-sm-4">
        <?= $form
            ->field($model, 'msg_subject')
            ->widget(Select2::classname(), $aSubject) ?>
    </div>

    <div class="col-sm-8">
        <?= $form
            ->field($model, 'alltags')
            ->widget(Select2::classname(), $aTags)
        //    ->field($model, 'alltags')
        //    ->widget(Select2::classname(), $aFieldParam['tags'])
        ?>
    </div>

    <div class="clearfix"></div>

    <div class="col-sm-4">
        <?= $form
            ->field($model, 'msg_empl_id')
            ->widget(Select2::classname(), $aAnswerUser)
        ?>
    </div>

    <div class="col-sm-4">
        <?= $form
            ->field($model, 'answers')
            ->widget(Select2::classname(), $aAnswerUser)
        ?>
    </div>

    <div class="col-sm-4">
        <?= $form
            ->field($model, 'msg_curator_id')
            ->widget(Select2::classname(), array_merge($aAnswerUser, ['options' => ['multiple' => true, 'placeholder' => 'Выберите контролера ...',],]))
        ?>
    </div>

    <div class="clearfix"></div>

    <div class="col-sm-4">
        <?= $form
            ->field($model, 'startdate')
            ->widget(
                DatePicker::classname(),
                [
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'options' => ['placeholder' => 'Начальная дата'],
                    'language' => 'ru',
                    'pluginOptions' => [
                        'autoclose'=>true
                    ],
                ]
            )
        ?>
    </div>

    <div class="col-sm-4">
        <?= $form
            ->field($model, 'finishdate')
            ->widget(
                DatePicker::classname(),
                [
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'options' => ['placeholder' => 'Конечная дата'],
                    'language' => 'ru',
                    'pluginOptions' => [
                        'autoclose'=>true
                    ],
                ]
            )
        ?>
    </div>

    <div class="clearfix"></div>

    <div class="col-sm-12">
        <?= $form
            ->field($model, 'fieldslist')
            ->widget(Select2::classname(), $aFieldlist)
        ?>
    </div>

    <div class="clearfix"></div>
    <div class="col-sm-3">
        <div class="form-group" style="margin-top: 2em;">
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-success btn-block', ]) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
