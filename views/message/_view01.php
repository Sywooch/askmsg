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

$aHiddenMessageFlags = [
    Msgflags::MFLG_NEW,
    Msgflags::MFLG_INT_NOSOGL,
    Msgflags::MFLG_INT_NEWANSWER,
    Msgflags::MFLG_INT_FIN_INSTR,
    Msgflags::MFLG_INT_INSTR,
    Msgflags::MFLG_INT_REVIS_INSTR,
    Msgflags::MFLG_NOSHOW,
];

if( Yii::$app->user->isGuest && $model->isHidden() ) {
    // не показываем неавторизованным ниего из ненужного
    return;
}

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

<?php $this->beginBlock('footerdata'); ?>
<div class="col-sm-6 no-horisontal-padding">
    <?php if( !empty($model->msg_empl_id)  ): ?>
        <?php $bShowFooter = true; ?>
        <div class="col-sm-3"><strong>Исполнитель</strong></div>
        <div class="col-sm-9">
            <?= Html::encode($model->employee->getFullName()) ?>
            <span class="dopline">
                <?= Html::encode($model->employee->us_workposition) ?>
            </span>
        </div>
    <?php endif; ?>
    <?php if( !empty($model->msg_curator_id)  ): ?>
        <?php $bShowFooter = true; ?>
        <div class="col-sm-3"><strong>Контроль <span style="font-size: 0.8em; /* color: #cccccc; */">исполнения</span></strong></div>
        <div class="col-sm-9">
            <?= Html::encode($model->curator->getFullName()) ?>
            <span class="dopline">
                <?= Html::encode($model->curator->us_workposition) ?>
            </span>
        </div>
    <?php endif; ?>
    <?php if( $isDopFields && (count($model->answers) > 0) ): ?>
        <div class="clearfix"></div>
        <div class="col-sm-3"><strong>Соисполнитель</strong></div>
        <div class="col-sm-9">
            <?php foreach( $model->answers As $k=>$ob ): ?>
                <?= $ob->getFullName() ?>
                <span class="dopline">
                    <?= Html::encode($ob->us_workposition) ?>
                </span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="col-sm-6 no-horisontal-padding">
    <?php if( !empty($model->msg_empl_command)  ): ?>
        <?php $bShowFooter = true; ?>
        <div class="col-sm-3"><strong>Поручение</strong></div>
        <div class="col-sm-9">
            <?= Html::encode($model->msg_empl_command) ?>
        </div>
        <div class="clearfix"></div>
    <?php endif; ?>

    <?php if( !empty($model->msg_comment) && $isDopFields ): ?>
        <div class="col-sm-12">
            <div class="alert alert-info" role="alert">
                <span aria-hidden="true" class="glyphicon glyphicon-info-sign"></span>
                <?= Html::encode($model->msg_comment) ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if( !empty($model->msg_empl_remark) && $isDopFields ): ?>
        <div class="col-sm-12">
            <div class="alert alert-danger" role="alert">
                <span aria-hidden="true" class="glyphicon glyphicon-exclamation-sign"></span>
                <?= Html::encode($model->msg_empl_remark) ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php $this->endBlock(); ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="col-sm-6 no-horisontal-padding">
            <div class="col-sm-3">
                <?= '№ ' . $model->msg_id . ' ' ?>
                <span class="dopline"><?= date('d.m.Y H:i', strtotime($model->msg_createtime)) ?></span>
            </div>
            <div class="col-sm-9">
                <strong><?= Html::encode($model->getFullName()) ?></strong>
                <span class="dopline"><?= Html::encode($model->msg_pers_org) ?></span>
            </div>
        </div>
        <div class="col-sm-6 no-horisontal-padding">
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="panel-body hidemoretext" style="padding-bottom: 32px;">
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
                <?php
                if( $model->msg_answertime !== null ) {
                    ?>
                    <span class="dopline"><?= date('d.m.Y', strtotime($model->msg_answertime)) ?></span>
                <?php
                }
                ?>
            </div>
        <?php endif; ?>

    </div>

    <?php if($bShowFooter): ?>
        <div class="panel-footer">
            <?= $this->blocks['footerdata'] ?>
            <div class="clearfix"></div>
        </div>
    <?php endif; ?>
</div>

