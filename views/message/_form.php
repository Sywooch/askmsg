<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use yii\web\JsExpression;
use yii\web\View;

use kartik\select2\Select2;

use app\models\Regions;
use app\models\Msgflags;
use app\models\User;
use app\models\Rolesimport;
use app\models\Tags;
use app\models\Message;
use app\models\File;

use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model app\models\Message */
/* @var $form yii\widgets\ActiveForm */

/*
    <?= $form->field($model, 'msg_createtime')->textInput() ?>
    <?= $form->field($model, 'msg_active')->textInput() ?>
    <?= $form->field($model, 'msg_comment')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'msg_empl_id')->textInput() ?>
    <?= $form->field($model, 'msg_empl_command')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'msg_empl_remark')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'msg_answer')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'msg_answertime')->textInput() ?>
    <?= $form->field($model, 'msg_oldcomment')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'msg_flag')->textInput() ?>

*/

echo '<!-- ';
if( $model->hasErrors() ) {
    echo str_replace("\n", "<br />\n", print_r($model->getErrors(), true));
}
else {
    echo "No errors";
}
echo '-->' . "\n";

$isModerate = $model->scenario == 'moderator';

?>

<div class="message-form">

    <?php $form = ActiveForm::begin([
            'id' => 'message-form',
            'layout' => 'horizontal',
            'options'=>[
                'enctype'=>'multipart/form-data'
            ],
            'fieldConfig' => [
//                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                'horizontalCssClasses' => [
                    'label' => 'col-sm-3',
                    'offset' => 'col-sm-offset-3',
                    'wrapper' => 'col-sm-9',
//                    'error' => '',
                    'hint' => 'col-sm-9 col-sm-offset-3',
                ],
            ],
    ]);


    if( $isModerate ) {
        echo $form->errorSummary([$model]);
    }

/*
    <div class="col-sm-4">
    </div>
    <div class="clearfix"></div>

*/
    ?>

    <?php
    /************************************************************************************************
     *
     * Часть модератора
     *
     */
    if( $isModerate ):
    ?>
        <?= $form->field($model, 'msg_flag', ['template' => "{input}", 'options' => ['tag' => 'span']])->hiddenInput();  ?>


        <div class="col-sm-6">
            <?php
                $aAnsw = User::getGroupUsers(Rolesimport::ROLE_ANSWER_DOGM, '', '{{val}}');
            ?>
            <?= $form
                ->field($model, 'msg_empl_id')
//                ->field($model, 'employer')
                ->widget(Select2::classname(), [
                    'data' => $aAnsw,
                    'language' => 'ru',
                    'options' => ['placeholder' => 'Выберите ответчика ...'],
                    'pluginOptions' => [
                        'allowClear' => true,
//                        'formatResult' => new JsExpression('function(object, container, query){ console.log("format: ", object, container, query); container.append(object.text);  }'),
                    ],
                    'pluginEvents' => [
//                        'change' => 'function(event) { jQuery("#'.Html::getInputId($model, 'msg_empl_id').'").val(event.val); console.log("change", event); }',
//                        'select2-selecting' => 'function(event) { console.log("select2-selecting", event); }',
                    ],
                ])
            ?>
        </div>

        <div class="col-sm-6">
            <?= $form
                ->field($model, 'answers')
                ->widget(Select2::classname(), [
                    'data' => $aAnsw,
                    'language' => 'ru',
                    'options' => [
                        'multiple' => true,
                        'placeholder' => 'Выберите соответчика ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ])
            //                ->dropDownList($aAnsw, ['multiple' => true])
            ?>
        </div>

        <div class="col-sm-6">
            <?= $form
                ->field($model, 'msg_empl_command')
                ->textarea()
                ->hint('Текст поручения будет виден всем посетителям при публикации обращения на сайте');
            ?>
        </div>

        <div class="col-sm-6">
            <?= $form
                ->field($model, 'msg_comment')
                ->textarea()
                ->hint('Текст комментария будет виден только ответчику и модератору');
            ?>
        </div>

        <div class="col-sm-6">
            <?= $form
                ->field($model, 'msg_empl_remark')
                ->textarea()
                ->hint('Текст замечания будет виден только ответчику и модератору');
            ?>
        </div>

        <div class="col-sm-6">
            <?= $form
                ->field($model, 'alltags')
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
                ])
            //                ->dropDownList($aAnsw, ['multiple' => true])
            ?>
        </div>

        <?php if( !empty($model->msg_answer)  ): ?>
        <div class="col-sm-12 thumbnail " id="id_answer" style="display: none;">
            <label for="message-msg_pers_text" class="control-label col-sm-1">Ответ</label>
            <div style="clear: both;">
            <?= $model->msg_answer ?>
            </div>
        </div>
        <?php endif; ?>

    <?php
    endif; // if( $isModerate ):
    /**
     *
     * Окончание части модератора
     *
     ************************************************************************************************/
    ?>


    <?php
    /************************************************************************************************
     *
     * Часть пользователя
     *
     */
    if( $isModerate ):
    ?>
        <div id="id_userformpart" style="display: none; clear: both; border: 1px solid #777777; border-radius: 4px; background-color: #eeeeee; padding-top: 2em; padding-bottom: 2em; margin-bottom: 2em;">
    <?php
    endif; // if( $isModerate ):
    ?>

    <div class="col-sm-12">
        <?= $form
            ->field(
                $model,
                'msg_subject',
                [
                    'horizontalCssClasses' => [
                    'label' => 'col-sm-1',
                    'offset' => 'col-sm-offset-1',
                    'wrapper' => 'col-sm-11',
                ],
            ])
            ->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_SUBJECT), 'tag_id', 'tag_title'),
                'language' => 'ru',
//                'disabled' => $isModerate,
//                'readonly' => $isModerate,
                'options' => [
                    'placeholder' => 'Выберите тему сообщения ...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ])

        ?>
    </div>

    <?php
    if( $isModerate ):
    ?>
        <div class="col-sm-12">
            <?= $form->field(
                $model,
                'msg_pers_org',
                [
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-1',
                        'offset' => 'col-sm-offset-1',
                        'wrapper' => 'col-sm-11',
                    ],
                    'inputOptions' => [
                        'disabled' => true,
                    ]
                ]
            )->textInput(['maxlength' => 255]) ?>
        </div>
    <?php
    endif; // if( $isModerate ):
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
        <?= $form->field(
            $model,
            'ekis_id'
        )
        ->widget(Select2::classname(), [
//            'data' => [],
            'language' => 'ru',
/*
            'scrollable' => true,
            'dataset' => [
                [
                    'remote' => [
                        'url' => Url::to(['user/answerlist', 'query'=>'QRY']),
                        'wildcard' => 'QRY',
                    ],
                    'displayKey' => 'val',

                    'templates' => [
                        'suggestion' => new JsExpression("Handlebars.compile('<p>{{val}}<br /><span style=\"color: #777777;\">{{pos}}</span></p>')"),
                    ],

                ]
            ],


   function repoFormatResult(repo) {
      var markup = '<div class="row-fluid">' +
         '<div class="span2"><img src="' + repo.owner.avatar_url + '" /></div>' +
         '<div class="span10">' +
            '<div class="row-fluid">' +
               '<div class="span6">' + repo.full_name + '</div>' +
               '<div class="span3"><i class="fa fa-code-fork"></i> ' + repo.forks_count + '</div>' +
               '<div class="span3"><i class="fa fa-star"></i> ' + repo.stargazers_count + '</div>' +
            '</div>';

      if (repo.description) {
         markup += '<div>' + repo.description + '</div>';
      }

      markup += '</div></div>';

      return markup;
   }


*/
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
/*
                        console.log("formatResult() item = ", item);
                        var markup = \'<div class="row-fluid">\'
                            + item.text
                            + \'<div class="span3"><i class="fa fa-star"></i>\' + item.district + \'</div>\'
                            + \'</div>\';
                        return markup; // item.text;
*/
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
        . $form->field(
            $model,
            'msg_pers_org',
            ['template' => "{input}", 'options' => ['tag' => 'span']]
        )->hiddenInput()
        . $form->field(
            $model,
            'msg_pers_region',
            ['template' => "{input}", 'options' => ['tag' => 'span']]
        )->hiddenInput()
        ?>
    </div>


    <div class="clearfix"></div>

    <div class="col-sm-12">
    <?= $form->field(
        $model,
        'msg_pers_text',
        [
//            'template' => "{input}\n{hint}\n{error}",
            'horizontalCssClasses' => [
                'label' => 'col-sm-1',
                'offset' => 'col-sm-offset-1',
                'wrapper' => 'col-sm-11',
            ],
        ])
        ->textarea(['rows' => 6]) ?>
    </div>

    <?php
    if( $model->isNewRecord ):
    ?>
    <div class="col-sm-12">
        <?= $form->field(
            $model,
            'file[]',
            [
//            'template' => "{input}\n{hint}\n{error}",
                'horizontalCssClasses' => [
                    'label' => 'col-sm-1',
                    'offset' => 'col-sm-offset-1',
                    'wrapper' => 'col-sm-11',
                ],
            ])
            ->widget(
                FileInput::classname(),
                [
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
                ]) ?>
    </div>

    <?php
    else:
        $aFiles = $model->getUserFiles(true);
        if( count($aFiles) > 0 ):
            ?>
            <div class="col-sm-12">
            <label for="message-msg_pers_text" class="control-label col-sm-1">Файлы</label>
            <div class="col-sm-11">
            <?php
            foreach($aFiles As $oFile):
                /** @var File  $oFile */
                echo Html::a(
                    $oFile->file_orig_name,
                    $oFile->getUrl(),
                    ['class' => 'btn btn-default']
                );
            endforeach;
            ?>
            <div class="clearfix"></div>
            </div>
            </div>
        <?php
        endif;
    ?>
    <?php
    endif; // if( $model->countAvalableFile() > 0 ):
    ?>
    <div class="clearfix"></div>

    <?php
    if( $isModerate ):
    ?>
        </div>
    <?php
    endif; // if( $isModerate ):
    /**
     *
     * Окончание части пользователя
     *
     ************************************************************************************************/
    ?>

    <div class="col-sm-12">
        <div class="form-group">
                <?php
                /**
                 *
                 * Тут кусочек для кнопок модератора, чтобы он нажимал и менял флаг
                 *
                 */
                    if( $isModerate ):
                        $aOp = array_reduce(
                            Msgflags::getStateTrans($model->msg_flag),
                            function ( $carry , $item ) {
                                $sTitle = Msgflags::getStateTitle($item, 'fl_command');
                                if( $sTitle != '' ) {
                                    $aFlagData = Msgflags::getStateData($item);
                                    $carry[$item] = ['title' => $sTitle, 'hint' => isset($aFlagData['fl_hint']) ? $aFlagData['fl_hint'] : '--'];
                                }
                                return $carry;
                            },
                            []
                        );
                    ?>
            <div class="col-sm-2"><?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?></div>

                <div class="col-sm-10">
                    <div class="form-group">
                        <!-- label for="message-msg_pers_text" class="control-label col-sm-3">&nbsp;</label -->
                        <div class="col-sm-3">
                            <a href="#" class="btn btn-default togglepart" id="toggle_userformpart" style="margin-bottom: 14px;">Показать Обращение</a>
                        </div>

                        <?php if( !empty($model->msg_answer)  ): ?>
                            <div class="col-sm-3">
                                <a href="#" class="btn btn-default togglepart" id="toggle_answer">Показать Ответ</a>
                            </div>
                        <?php endif; ?>

                    </div>

                </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group" style="margin-top: 2em;">
                <?php
                        foreach($aOp As $k=>$aData):
                ?>
                            <div id="<?= "buttongroup_" . $k ?>">
                                <div class="col-sm-4">
                                <?= Html::submitButton(
                                'Сохранить и ' . $aData['title'],
                                ['class' => 'btn btn-default changeflag', 'id' => 'buttonsave_' . $k, 'style' => 'margin-bottom: 1em;']) ?>
                                </div>
                                <div class="col-sm-8 help-block">
                                    <?= $aData['hint'] ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                <?php
                        endforeach;
                /**
                 *
                 * Окончание кнопок модератора
                 *
                 */
                    else:
                ?>
                    <label for="message-msg_pers_text" class="control-label col-sm-1">&nbsp;</label>
                    <div class="col-sm-6">
                        <?= Html::submitButton($model->isNewRecord ? 'Отправить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>
                <?php
                    endif; // if( $isModerate ):
                ?>
        </div>
    </div>

    <?php ActiveForm::end();


    $sFlagId = Html::getInputId($model, 'msg_flag');
    $sCommandId = Html::getInputId($model, 'msg_empl_command');
    $sRemarkId = Html::getInputId($model, 'msg_empl_remark');
    $sMsgTextId = Html::getInputId($model, 'msg_pers_text');
    $nMsgTextLen = Message::MAX_PERSON_TEXT_LENGTH;

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

    // Показываем/скрываем сообщение пользователя и ответ
    $sJs .=  <<<EOT
//var oUserPart = jQuery(".togglepart");
jQuery(".togglepart").on("click", function(event){
    var ob = jQuery(this),
        id = ob.attr("id"),
        dest = id.split("_").pop(),
        aText = ob.text().split(" "),
        oDest = jQuery("#id_" + dest);
    event.preventDefault();
    aText[0] = oDest.is(":visible") ? "Показать" : "Скрыть";
    ob.text(aText.join(" "));
    oDest.toggle();
    return false;
});
EOT;

    // Меняем флаг сообщения в зависимости от нажатой кнопки
    $sJs .=  <<<EOT
var oButtons = jQuery('.changeflag'),
    oFlag = jQuery("#{$sFlagId}"),
    oCommand = jQuery("#{$sCommandId}"),
    oRemark = jQuery("#{$sRemarkId}");

oButtons.on("click", function(event){
    event.preventDefault();
    var ob = jQuery(this),
        nFlag = parseInt(ob.attr("id").split("_")[1]);
//    console.log("id = " + ob.attr("id").split("_")[1]);
    oFlag.val(nFlag);
    jQuery("#message-form").submit();
    return true;
});
EOT;

    // Фильтруем видимость кнопок в зависимости от смены состояния полей замечаний и поручений
    // новая запись: оставляем кнопки с поручениями, если заполнено поручение
    $nFlagNewMsg = Msgflags::MFLG_NEW;
    $nFlagInstr = Msgflags::MFLG_SHOW_INSTR;
    $nFlagInstrInt = Msgflags::MFLG_INT_INSTR;
    $sJs .=  <<<EOT
var filterButtons = function() {
    if( {$nFlagNewMsg} == parseInt(oFlag.val()) ) {
        if( oCommand.val().length > 0 ) {
            oButtons.each(function(index, ob){
                var ob = jQuery(this),
                    nId = parseInt(ob.attr("id").split("_")[1]),
                    oGroup = jQuery("#buttongroup_" + nId);
                console.log("flag = " + nId + " - " + ob.attr("id"));
                if( (nId != {$nFlagInstrInt}) && nId != {$nFlagInstr} ) {
                    oGroup.hide();
                }
                else {
                    oGroup.show();
                }
            });
        }
        else {
            oButtons.show();
        }
        console.log("Command = " + oCommand.val());
    }
};

oCommand.on("keyup", function(event){
    filterButtons();
});
EOT;

        $this->registerJs($sJs, View::POS_READY, 'toggleuserpart');
    // функция форматирования результатов в список для select2
    $sJs =  <<<EOT
var formatSelect = function(item, text, description) {
    return  item[text] + "<span>" + item[description] + "</span>";
}

EOT;
    $this->registerJs($sJs, View::POS_END , 'showselectpart');

    ?>

</div>
