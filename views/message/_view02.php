<?php

use yii\helpers\Html;
use yii\web\View;

use app\models\Msgflags;
use app\models\Rolesimport;

/* @var $this yii\web\View */
/* @var $model app\models\Message */
/* @var $form yii\widgets\ActiveForm */
/* @var $oSubj app\models\Tags */

$isModerate = Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM);
$isAnswer = Yii::$app->user->can(Rolesimport::ROLE_ANSWER_DOGM);
$isDopFields = $isModerate || $isAnswer;
$oSubj = $model->subject;
$isShowAnswer = !empty($model->msg_answer)
    && (($model->msg_flag == Msgflags::MFLG_SHOW_ANSWER) || Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM));
$bShowFooter = false;

$nMaxTextHeight = 280;

// Показываем/скрываем сообщение пользователя и ответ
$sJs =  <<<EOT
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

jQuery(".hidemoretext").each(function(index, element){
    var ob = jQuery(this),
        h = ob.height(),
        nSmallSize = {$nMaxTextHeight},
        nMaxSize = nSmallSize + 28,
        oAns = ob.find(".answerblock");
    if( h > nMaxSize ) {
        if( oAns.length > 0 ) {
//            console.log(oAns.attr("id") + ": ", oAns.position(), nSmallSize);
            nSmallSize = Math.min(oAns.offset().top - ob.offset().top - 28, nSmallSize);
//            console.log(oAns.attr("id") + " -> " + nSmallSize);
        }
        ob
            .append("<div class=\"showmoretext\" href=\"#\"><span class=\"glyphicon glyphicon-chevron-down\"></span></div>")
            .css({position: "relative", overflow: "hidden"})
            .height(nSmallSize)
            .find(".showmoretext").on("click", function(event){
                var olink = jQuery(this);
                event.preventDefault();
                if( ob.height() > nMaxSize ) {
                    ob.height(nSmallSize);
                    olink.html("<span class=\"glyphicon glyphicon-chevron-down\"></span>");
                }
                else {
                    ob.css({height: "100%"});
                    olink.html("<span class=\"glyphicon glyphicon-chevron-up\"></span>");
                }
                return false;
            });
    }
});
EOT;

$this->registerJs($sJs, View::POS_READY, 'toggleuserpart');

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="col-sm-6 no-horisontal-padding">
            <div class="col-sm-12">
                <strong><?= Html::encode($model->msg_pers_org) ?></strong>
            </div>
        </div>
        <div class="col-sm-6 no-horisontal-padding">
            <div class="col-sm-3"><strong>Исполнитель</strong></div>
            <div class="col-sm-9">
                <?= Html::encode($model->employee->getFullName()) ?>
                <span class="dopline">
                <?= Html::encode($model->employee->us_workposition) ?>
            </span>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="panel-body hidemoretext">
        <?php
        if( $isDopFields ) :
        ?>
            <?php
            if( $oSubj !== null ) :
            ?>
                <h4>
                    <strong><?= Html::encode($oSubj->tag_title) ?></strong>
                </h4>
            <?php
            endif;
            ?>
        <?php
        endif;
        ?>

        <p class="text-justify">
            <?= str_replace("\n", "<br />\n", $model->msg_pers_text) ?>
            <?php /* if( $isShowAnswer  ): ?>
                <br />
                <?= Html::a('Показать ответ', '#', ['class' => 'togglepart btn btn-default', 'id'=>'toggle_answer'.$model->msg_id]) ?>
            <?php endif; */ ?>
        </p>

        <?php
        $aFiles = $model->getUserFiles(true);
        if( (count($aFiles) > 0) && !Yii::$app->user->isGuest ):
            ?>
            <div>
                <strong>Файлы: </strong>
                <?php foreach($aFiles As $oFile):
                    /** @var File  $oFile */ ?>
                    <?= Html::a(
                        $oFile->file_orig_name,
                        $oFile->getUrl()
                    ) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if( $isShowAnswer  ): ?>
            <div id="id_answer<?= $model->msg_id ?>" class="breadcrumb answerblock">
                <h4>Ответ</h4>
                <?= $model->msg_answer ?>
                <?php
                //  style="display: none;"
                $aFiles = $model->getUserFiles(false);
                $nFilesExists = count($aFiles);
                if( $nFilesExists > 0 ):
                    ?>
                    <div class="listcommand">
                        <strong>Файлы к ответу: </strong>
                        <?php
                        foreach($aFiles As $oFile):
                            /** @var File  $oFile */
                            ?>
                            <?= Html::a(
                            $oFile->file_orig_name,
                            $oFile->getUrl()
                        )
                            ?>
                        <?php
                        endforeach;
                        ?>
                    </div>
                <?php
                endif; // if( $nFilesExists > 0 ):
                ?>
            </div>
        <?php endif; ?>

    </div>

</div>

