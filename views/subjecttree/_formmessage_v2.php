<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
//use yii\bootstrap\ActiveForm;
use yii\web\View;
use app\assets\JqueryfilerAsset;

/* @var $this yii\web\View */
/* @var $model app\models\SubjectTree */
/* @var $formmodel app\models\MessageTreeForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $step integer */
/* @var $subjectid integer */

JqueryfilerAsset::register($this);

// Yii::info(__FILE__);
$aFieldParam = [
    'filefield' => [
    //            'template' => "{input}\n{hint}\n{error}",
//        'horizontalCssClasses' => [
//            'label' => 'col-sm-1',
//            'offset' => 'col-sm-offset-1',
//            'wrapper' => 'col-sm-11',
//        ],
//        'hintOptions' => [
//            'class' => 'col-sm-11 col-sm-offset-1',
//        ],
    ],
];

?>

<div class="subject-tree-message-form">
    <?= '' // 'step = ' . $step ?>

    <?php $form = ActiveForm::begin([
//        'action' => ['subjecttree/stepmasg', 'id' => $model->subj_id],
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validateOnType' => false,
        'validateOnSubmit' => true,
    ]); ?>

    <?= Html::hiddenInput('step', $step) ?>

    <?php

    echo $this->render(
        '_formmessage_userdata',
        [
            'form' => $form,
            'formmodel' => $formmodel,
            'model' => $model,
            'step' => $step,
        ]
    );
/*
    <div class="step_1" style="display: <?= ($step == 1) ? 'block' : 'none' ?>;">
        <div class="row">
            <div class="col-sm-3"><?= $form->field($formmodel, 'msg_pers_name')->textInput() ?></div>
            <div class="col-sm-3"><?= $form->field($formmodel, 'msg_pers_secname')->textInput() ?></div>
            <div class="col-sm-3"><?= $form->field($formmodel, 'msg_pers_lastname')->textInput() ?></div>
        </div>

        <div class="row">
            <div class="col-sm-3"><?= $form->field($formmodel, 'msg_pers_email')->textInput() ?></div>
            <div class="col-sm-3"><?= $form->field($formmodel, 'msg_pers_phone')->textInput() ?></div>
        </div>
    </div>

*/

    ?>


    <div class="step_2" style="display: <?= ($step == 2) ? 'block' : 'none' ?>;">
        <?= $this->render(
            'treeform',
            [
                'form' => $form,
                'formmodel' => $formmodel,
                'model' => $model,
                'child' => ($step == 2) ? $child : [],
                'parents' => ($step == 2) ? $parents : [],
            ]
        ) ?>
    </div>

    <div class="step_3" style="display: <?= ($step == 3) ? 'block' : 'none' ?>;">
        <div class="row">
            <div class="col-sm-12" style="display: <?= $formmodel->is_user_variant > 0 ? 'block' : 'none' ?>;">
                <?= $form->field($formmodel, 'msg_pers_subject')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?= $form->field($formmodel, 'msg_pers_text')->textarea(['rows' => 6]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12"><?= $form
                    ->field($formmodel, 'file[]', $aFieldParam['filefield'])
                    ->fileInput(['multiple' => true])
                    ->hint('Максимальный размер файла: '
                        . sprintf("%.1f Mb", Yii::$app->params['message.file.maxsize'] / 1000000)
                        . ', Допустимые типы файлов: '
                        . implode(',', Yii::$app->params['message.file.ext'])
                    )
            ?></div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <?php /* <div class="col-sm-2" style="display: none;<?= '' // ($step > 1) ? 'block' : 'none' ?>;"><?= Html::submitButton('Назад', ['class' => 'btn btn-success', 'name' =>'prev', ]) ?></div> */ ?>
            <div class="col-sm-2"><?= Html::submitButton(($step < 3) ? 'Далее' : 'Отправить', ['class' => 'btn btn-success', 'name' =>'next', 'id' => 'nextactionbutton', ]) ?></div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$sSubjectName = Html::getInputName($formmodel, 'subject_id');
//$sSubjectName = str_replace(['[', ']'], ['[', ']'], $sSubjectName);
$nSubjectId = $formmodel->subject_id;

$sJs = <<<EOT
var aRadioButtons = jQuery(".radiobutton"),
    oNext = jQuery('#nextactionbutton');

aRadioButtons.each(function(index, el) {
    var oRadio = jQuery(this),
        oLabel = oRadio.parent(),
        sLinkClass = "radiolink",
        sClass = "btn btn-default " + sLinkClass,
        sActiveClass = "btn-success",
        oLink = jQuery('<a href="#" class="' + sClass + '" data-value="' + oRadio.val() + '"></a>').text(oLabel.text());

    oLabel.wrap( "<div class='buttonblock'></div>");
    oLabel.before(oLink);
    oLabel.hide();
    oLink.on(
        "click",
        function(event) {
            var ob = jQuery(this);
            event.preventDefault();
            aRadioButtons.prop("checked", false);
            jQuery("." + sLinkClass).removeClass(sActiveClass);
            oRadio.prop("checked", true);
            ob.addClass(sActiveClass);
            oNext.trigger('click');
//            console.log("Set " + oRadio.val());
            return false;
        }
    );
});

var setRadioButtons = function(classRadio, linkClass) {
    var aRadioButtons = jQuery("." + classRadio); // radioask

    aRadioButtons.each(function(index, el) {
        var oRadio = jQuery(this),
            oLabel = oRadio.parent(),
            sLinkClass = linkClass, // "radioasklink"
            sClass = "btn btn-default btn-block " + sLinkClass,
            sActiveClass = "btn-success",
            oLink = jQuery('<a href="#" class="' + sClass + (oRadio.prop("checked") ? (" " + sActiveClass) : "") + '"></a>').text(oLabel.text());

        oLabel.wrap( "<div class='col-sm-6'></div>");
        oLabel.before(oLink);
        oLabel.hide();
        oLink.on(
            "click",
            function(event) {
                var ob = jQuery(this);
                event.preventDefault();
                aRadioButtons.prop("checked", false);
                jQuery("." + sLinkClass).removeClass(sActiveClass);
                oRadio.prop("checked", true);
                ob.addClass(sActiveClass);
//                console.log("Set " + oRadio.val());
                oNext.trigger('click');
                return false;
            }
        );
    });
};

var obOther = jQuery(".otherradiobutton");
obOther.each(function(index, el) {
    var oRadio = jQuery(this),
        oLabel = oRadio.parent(),
        sLinkClass = "otherradiolink",
        sClass = "btn btn-default " + sLinkClass,
        sActiveClass = "btn-success",
        oLink = jQuery('<a href="#" class="' + sClass + '"></a>').text(oLabel.text());

    oLabel.wrap( "<div class='buttonblock'></div>");
    oLabel.before(oLink);
    oLabel.hide();
    oLink.on(
        "click",
        function(event) {
            var ob = jQuery(this);
            event.preventDefault();
            aRadioButtons.prop("checked", false);
            jQuery("." + sLinkClass).removeClass(sActiveClass);
            oRadio.prop("checked", true);
            ob.addClass(sActiveClass);
            jQuery('input[name="{$sSubjectName}"]:first').val({$nSubjectId});
            oNext.trigger('click');
//            console.log("Set " + oRadio.val());
            return false;
        }
    );
});

setRadioButtons("satisfyclass", "satrisfylink");
setRadioButtons("askdirclass", "askdirlink");
EOT;

$this->registerJs($sJs, View::POS_READY, 'radiobattonchange');

$sExt = '["' . implode('","', Yii::$app->params['message.file.ext']) . '"]';
$sFileExt = implode(', ', Yii::$app->params['message.file.ext']);
$nMaxSize = Yii::$app->params['message.file.maxsize'] / 1000000;
$sJs = <<<EOT
$('#messagetreeform-file').filer({
        limit: 1,
        maxSize: {$nMaxSize},
        extensions: {$sExt},
        changeInput: true,
        showThumbs: true,
        appendTo: null,
        theme: "default",
        templates: {
            box: '<ul class="jFiler-item-list"></ul>',
            item: '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
                                <div class="jFiler-item-thumb">\
                                    <div class="jFiler-item-status"></div>\
                                    <div class="jFiler-item-info">\
                                        <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                    </div>\
                                    {{fi-image}}\
                                </div>\
                                <div class="jFiler-item-assets jFiler-row">\
                                    <ul class="list-inline pull-left">\
                                        <li>{{fi-progressBar}}</li>\
                                    </ul>\
                                    <ul class="list-inline pull-right">\
                                        <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                    </ul>\
                                </div>\
                            </div>\
                        </div>\
                    </li>',
            itemAppend: '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
                                <div class="jFiler-item-thumb">\
                                    <div class="jFiler-item-status"></div>\
                                    <div class="jFiler-item-info">\
                                        <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                    </div>\
                                    {{fi-image}}\
                                </div>\
                                <div class="jFiler-item-assets jFiler-row">\
                                    <ul class="list-inline pull-left">\
                                        <span class="jFiler-item-others">{{fi-icon}} {{fi-size2}}</span>\
                                    </ul>\
                                    <ul class="list-inline pull-right">\
                                        <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                    </ul>\
                                </div>\
                            </div>\
                        </div>\
                    </li>',
            progressBar: '<div class="bar"></div>',
            itemAppendToEnd: false,
            removeConfirmation: true,
            _selectors: {
                list: '.jFiler-item-list',
                item: '.jFiler-item',
                progressBar: '.bar',
                remove: '.jFiler-item-trash-action',
            }
        },
        dragDrop: {
            dragEnter: null,
            dragLeave: null,
            drop: null,
        },
        addMore: true,
        clipBoardPaste: true,
        excludeName: null,
        beforeShow: function(){return true},
        onSelect: function(){},
        afterShow: function(){},
        onRemove: function(){},
        onEmpty: function(){},
        captions: {
            button: "Выберите файл",
            feedback: "Выбрано файлов для загрузки",
            feedback2: "Выбрано файлов",
            drop: "Перетащите сюда файлы для загрузки",
            removeConfirmation: "Удалить этот файл?",
            errors: {
                filesLimit: "Можно загрузить не более {{fi-limit}} файлов.",
                filesType: "Файлы только типов {$sFileExt} разрешены к загрузке.",
                filesSize: "{{fi-name}} слишком большой! Выберите файл до {{fi-maxSize}} MB.",
                filesSizeAll: "Слишком большие файлы выбрали! Пожалуйста ограничьте их размер {{fi-maxSize}} MB."
            }
        }
    });
EOT;
$this->registerJs($sJs, View::POS_READY, 'jqueryfiler');
