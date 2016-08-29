<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use app\models\MessageTreeForm;
use yii\web\View;
use kartik\select2\Select2;
use yii\widgets\MaskedInput;
use yii\web\JsExpression;
use app\assets\HelperscriptAsset;
use yii\helpers\Url;

use vova07\imperavi\Widget;


/* @var $this yii\web\View */
/* @var $model app\models\Message */
/* @var $form yii\widgets\ActiveForm */

HelperscriptAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\SubjectTree */
/* @var $formmodel app\models\MessageTreeForm */
/* @var $form yii\widgets\ActiveForm */

$sMsgTextId = Html::getInputId($formmodel, 'msg_pers_text');

$nMsgTextLen = MessageTreeForm::MAX_PERSON_TEXT_LENGTH;

// Показываем количество символов в сообщении
$sJs =  <<<EOT
var oMsgTextField = jQuery("#{$sMsgTextId}"),
    oLenIndicator = jQuery('<div>Осталось символов: </div>').addClass("textmsglength").append('<span />').insertAfter(oMsgTextField),
    showTextLength = function() {
        var sText = oMsgTextField.val(),
            nLen = sText.length;
        if( nLen > {$nMsgTextLen} ) {
            sText = sText.substr(0, {$nMsgTextLen});
            oMsgTextField.val(sText)
            nLen = sText.length;
        }
        oLenIndicator.find('span').text({$nMsgTextLen} - nLen);
    };
showTextLength();
oMsgTextField.on("keyup", function(event){
    showTextLength();
});

EOT;


$this->registerJs($sJs, View::POS_READY, 'countchars');

// функция форматирования результатов в список для select2
$sJs =  <<<EOT
var formatSelect = function(item, text, description) {
    return  item[text] + "<span class=\\"description\\">" + item[description] + "</span>";
}

EOT;
$this->registerJs($sJs, View::POS_END , 'showselectpart');

$aFieldParam = [
    'orgfield' => [
    //    'horizontalCssClasses' => [
    //        'label' => 'col-sm-1',
    //        'offset' => 'col-sm-offset-1',
    //        'wrapper' => 'col-sm-11',
    //    ],
        'inputOptions' => [
            'disabled' => true,
        ]
    ],
    'ekisid' => [
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
                    var sIdReg = "'.Html::getInputId($formmodel, 'msg_pers_region').'";
                    jQuery("#'.Html::getInputId($formmodel, 'msg_pers_org').'").val(event.added.text);
                    jQuery("#"+sIdReg).val(event.added.area_id);
//                    console.log("change", event);
//                    console.log("set " + sIdReg + " = " + event.added.area_id);
                }',
    ],

    'options' => [
//                    'multiple' => true,
        'placeholder' => 'Выберите учреждение ...',
    ],
],
];

?>

    <div class="step_1" style="display: <?= ($step == 1) ? 'block' : 'none' ?>;">
        <div class="row">
            <div class="col-sm-3"><?= $form->field($formmodel, 'msg_pers_name')->textInput() ?></div>
            <div class="col-sm-3"><?= $form->field($formmodel, 'msg_pers_secname')->textInput() ?></div>
            <div class="col-sm-3"><?= $form->field($formmodel, 'msg_pers_lastname')->textInput() ?></div>
        </div>

        <div class="row">
            <div class="col-sm-3"><?= $form->field($formmodel, 'msg_pers_email')->textInput() ?></div>
            <div class="col-sm-3"><?= $form->field($formmodel, 'msg_pers_phone')->widget(MaskedInput::className(),[
                    'name' => 'msg_pers_phone',
                    'mask' => '+7(999) 999-99-99'
                ]) ?></div>
            <div class="col-sm-3"><?= $form
                    ->field($formmodel, 'ekis_id')
                    ->widget(Select2::classname(), $aFieldParam['ekisid'])
                . $form
                    ->field($formmodel, 'msg_pers_org',['template' => "{input}", 'options' => ['tag' => 'span']])
                    ->hiddenInput()
                . $form
                    ->field($formmodel, 'msg_pers_region', ['template' => "{input}", 'options' => ['tag' => 'span']])
                    ->hiddenInput()
                ?>

            </div>
        </div>

    </div>


