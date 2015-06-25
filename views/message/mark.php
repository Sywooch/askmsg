<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
use app\models\Msgflags;
use app\models\Rolesimport;
use yii\helpers\Url;
use app\assets\JqueryfilerAsset;

/*

$enc = openssl_encrypt($str, 'bf-ecb', $key, true);
$dec = openssl_decrypt($enc, 'bf-ecb', $key, true);
echo(bin2hex($enc).PHP_EOL);

*/
/*
$key = '9876543210';
// $method = 'bf-ecb';
$method = 'aes-256-ecb';
for($i = 0; $i < 10; $i++ ) {
    $n = $model->msg_id + $i;
    $s = openssl_encrypt($n, $method, $key, true);
    echo $n . ' : ' . bin2hex($s) . ' = ' . openssl_decrypt($s, $method, $key, true) . "<br />\n";
}
*/

/* @var $this yii\web\View */
/* @var $model app\models\Message */

$this->title = 'Оценка ответа на обращение № ' . $model->msg_id . ' от ' . date('d.m.Y', strtotime($model->msg_createtime));
$this->params['breadcrumbs'] = [];
// $this->params['breadcrumbs'][] = ['label' => 'Обращения', 'url' => $url];
// $this->params['breadcrumbs'][] = $this->title;

$isShowAnswer = !empty($model->msg_answer)
    && (($model->msg_flag == Msgflags::MFLG_SHOW_ANSWER) || Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM));

JqueryfilerAsset::register($this);

$sExt = '["' . implode('","', Yii::$app->params['message.file.ext']) . '"]';
$sFileExt = implode(', ', Yii::$app->params['message.file.ext']);
$nMaxSize = Yii::$app->params['message.file.maxsize'] / 1000000;
$sJs = <<<EOT
$('#message-file').filer({
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

$aFieldParam = [
    'file' => [
        'options'=>[
            //                    'accept'=>'image/*',
            'multiple'=> !Yii::$app->user->isGuest
        ],
        'pluginOptions'=>[
            'uploadUrl' => Url::to(['file/upload']),
            'allowedFileExtensions' => Yii::$app->params['message.file.ext'],
            'maxFileCount' => 3,
            'showPreview' => true,
            'showCaption' => true,
            'showRemove' => true,
            'showUpload' => false,
        ]
    ],

    'filefield' => [
/*

        'horizontalCssClasses' => [
            'label' => 'col-sm-1',
            'offset' => 'col-sm-offset-1',
            'wrapper' => 'col-sm-11',
        ],
        'hintOptions' => [
            'class' => 'col-sm-11 col-sm-offset-1',
        ],
*/
    ],
];
// <strong></strong>
?>
<div class="message-mark">
    <div class="col-sm-6 col-sm-offset-3">
    <p>Здравствуйте, <?= $model->getShortName() ?>.</p>
    <p>На Ваше обращение № <?= $model->msg_id . ' от ' . date('d.m.Y', strtotime($model->msg_createtime)) ?> был дан ответ.
    <?php if($isShowAnswer) { ?>
        <a href="" id="id-show-msg-button">Показать</a>
    <?php } ?>
    </p>
    </div>

    <?php
    $form = ActiveForm::begin([
        'id' => 'message-mark-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validateOnType' => false,
        'options'=>[
            'enctype'=>'multipart/form-data'
        ],
    ]);
    ?>

    <div class="col-sm-6 col-sm-offset-3">
    <?= $form->field($model, 'msg_mark')->radioList($model->aMark) // , ['labelOptions' => ['style'=>'font-size: 1.4em;']] ?>
    <?= $form->field($model, 'testemail')->textInput(['maxlength' => 64, 'style'=>'width: 120px;'])->hint('Для проверки авторства обращения укажите проверочный код, который указан в письме об ответе на обращение.<span style="color: #f0f0f0">' . $model->getTestCode() . '</span>') // , на который пришло оповещение об ответе ?>
    <div id="id-marktext" style="display: none;">
    <?= $form->field($model, 'marktext')->textarea(['rows' => 8])->hint('Укажите, что именно Вас не устраивает в ответе.') ?>
    <?= $form
        ->field($model, 'file[]', $aFieldParam['filefield'])
        ->fileInput(['multiple' => true])
        ->hint('Максимальный размер файла: '
            . sprintf("%.1f Mb", Yii::$app->params['message.file.maxsize'] / 1000000)
            . ', Допустимые типы файлов: '
            . implode(',', Yii::$app->params['message.file.ext'])
        )
    ?>

    </div>
    </div>
    <div class="col-sm-9 col-sm-offset-3">
    <?= Html::submitButton('Отправить оценку', ['class' => 'btn btn-success']) // btn-block ?>
    <?= '' // Html::submitButton('Оценить ответ и написать обращение по ответу ', ['class' => 'btn btn-success', 'id'=>'id-button-new-message', 'name'=>'addmsg']) // btn-block, 'style'=>'display: none;' ?>
    </div>


    <?php ActiveForm::end();
    $sButtonId = 'message-msg_mark';
    $sJs =  <<<EOT
    if( !('decorateradio' in jQuery.fn) ) {
        jQuery.fn.decorateradio = function(options) {
            options = jQuery.extend({
                element: "button",  //элемент для добавления
                divclass: ["btn-group"],  //классы родительского элемента btn-group-justified
                btnclass: ["btn"],  //классы элемента
                activeclass: "btn-success",  //класс выбранного элемента
                passiveclass: "btn-default",  //класс невыбранных элементов
                valueclass: {} // класс для выбранных кнопок по значению
            }, options);
            var decorate = function() {
                var oDiv = jQuery(this), oResult = jQuery("<div />").attr({role: "group"});
                for(var i in options.divclass) {
                    oResult.addClass(options.divclass[i]);
                }

                oDiv
                    .find("input[type='radio']")
                    .each(function(index, el){
                        var o = jQuery(this),
                            v = o.val(),
                            activeClass = (v in options.valueclass) ? options.valueclass[v] : options.activeclass,
                            sClass = o.attr("checked") ? activeClass : options.passiveclass,
                            t = o.parents("label:first").text();
                        jQuery("<" + options.element + ">")
                            .attr({type: "button", "data-activeclass": activeClass})
                            .addClass("btn")
                            .addClass(sClass)
                            .css({"margin-right" : 0})
                            .text(t)
                            .on("click", function(event){
                                event.preventDefault();
                                o.trigger("click");
                                oResult.find(options.element).each(function(index, el){
                                    var ob = jQuery(this);
                                    ob.addClass(options.passiveclass).removeClass(ob.attr("data-activeclass"));
                                });
                                jQuery(this).removeClass(options.passiveclass).addClass(activeClass).trigger("blur");

                                return false;
                            })
                            .appendTo(oResult);
                    });
                oDiv
                    .children()
                    .css({display: "none"});
                oDiv
                    .append(oResult);
            };
            return this.each(decorate);
        }
    }

    jQuery("#{$sButtonId}")
        .decorateradio({
            valueclass: {0: "btn-danger"},
            // element: ""
            divclass: ["btn-group"] // , "btn-group-lg"
        })
        .find("input[type='radio']")
        .on("change", function(event){
            var ob = jQuery(this),
                oBut = jQuery("#id-marktext");

            if( ob.val() == 0 ) {
                oBut.show();
            }
            else {
                oBut.hide();
            }
        });
    jQuery("#{$sButtonId}").find("input[type='radio'][checked]").trigger("change");
    jQuery("#id-show-msg-button")
//        .show()
        .on("click", function(event){
            event.preventDefault();
            var ob = jQuery("#id-message-text"),
                oLink = jQuery(this),
                aText = oLink.text().split(" ");
            if( ob.is(":visible") ) {
                ob.hide();
                aText[0] = "Показать";
            }
            else {
                ob.show();
                aText[0] = "Скрыть";
            }
            oLink.text(aText.join(" "));
            return false;
        });

EOT;

    $this->registerJs($sJs, View::POS_READY, 'decorateradio');


    ?>
    <div class="clearfix" style="margin-bottom: 60px;"></div>

    <?= $isShowAnswer ?
        ('<div id="id-message-text" style="display: none;"><div class="clearfix"></div>' .
            $this->render(
                '_view02',
                [
                    'model' => $model,
                ]
            ) . '</div>') : ''
    ?>

    <?= '' /* $this->render($aData['form'], [
        'model' => $model,
    ])*/ ?>

</div>
