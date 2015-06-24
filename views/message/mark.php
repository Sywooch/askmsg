<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
use app\models\Msgflags;
use app\models\Rolesimport;

/* @var $this yii\web\View */
/* @var $model app\models\Message */

$this->title = 'Оценка ответа на обращение № ' . $model->msg_id . ' от ' . date('d.m.Y', strtotime($model->msg_createtime));
$this->params['breadcrumbs'] = [];
// $this->params['breadcrumbs'][] = ['label' => 'Обращения', 'url' => $url];
// $this->params['breadcrumbs'][] = $this->title;

$isShowAnswer = !empty($model->msg_answer)
    && (($model->msg_flag == Msgflags::MFLG_SHOW_ANSWER) || Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM));

// <strong></strong>
?>
<div class="message-mark">
    <div class="col-sm-6 col-sm-offset-3">
    <p>Здравствуйте, <?= $model->getShortName() ?>.</p>
    <p>На Ваше обращение № <?= $model->msg_id . ' от ' . date('d.m.Y', strtotime($model->msg_createtime)) ?> был дан ответ.</p>
    <?php if($isShowAnswer) { ?>
        <p><a href="" style="display: none;" id="id-show-msg-button" class="btn btn-default">Показать сообщение и ответ</a></p>
    <?php } ?>
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
//        'layout' => 'horizontal',
//        'options'=>[
//            'enctype'=>'multipart/form-data'
//        ],
//        'fieldConfig' => [
//                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
//            'horizontalCssClasses' => [
//                'label' => 'col-sm-3',
//                'offset' => 'col-sm-offset-3',
//                'wrapper' => 'col-sm-9',
//                    'error' => '',
//                'hint' => 'col-sm-9 col-sm-offset-3',
//            ],
//        ],
    ]);
    // , ['options' => ['class' => 'col-sm-6']]
    ?>

    <div class="col-sm-6 col-sm-offset-3">
    <?= $form->field($model, 'msg_mark')->radioList($model->aMark) // , ['labelOptions' => ['style'=>'font-size: 1.4em;']] ?>
    <?= $form->field($model, 'testemail')->textInput(['maxlength' => 64])->hint('Для проверки авторства обращения укажите Ваш email, который был указан при направлении обращения.') // , на который пришло оповещение об ответе ?>
    </div>
    <!-- div class="btn-group" role="group" aria-label="">
        <button type="button" class="btn btn-default">Left</button>
        <button type="button" class="btn btn-default">Right</button>
    </div -->
    <!-- div class="col-sm-3" -->
    <div class="col-sm-9 col-sm-offset-3">
    <?= Html::submitButton('Оценить ответ', ['class' => 'btn btn-success']) // btn-block ?>
    <?= Html::submitButton('Оценить ответ и написать обращение по ответу ', ['class' => 'btn btn-info', 'id'=>'id-button-new-message', 'name'=>'addmsg']) // btn-block, 'style'=>'display: none;' ?>
    </div>
    <!-- /div -->


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
                oBut = jQuery("#id-button-new-message");

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


    <div id="id-message-text" style="display: none;">
    <?= $isShowAnswer ?
    $this->render(
        '_view02',
        [
            'model' => $model,
        ]
    ) : ''
    ?>
    </div>
    <?= '' /* $this->render($aData['form'], [
        'model' => $model,
    ])*/ ?>

</div>
