<?php

use yii\helpers\Html;
use yii\web\View;
use mosedu\multirows\MultirowsWidget;
use yii\widgets\ActiveForm;
use app\models\Notificateact;

/* @var $this yii\web\View */
/* @var $model app\models\Notificateact */

// $this->title = 'Сисок действий';
// $this->params['breadcrumbs'][] = ['label' => 'Notificateacts', 'url' => ['index']];
// $this->params['breadcrumbs'][] = $this->title;

$aActions = Notificateact::find()->where('true')->orderBy('ntfd_message_age')->all();
$aAddNewRowId = '#add-action-link';

?>
<div class="notificateact-actlist">

    <div class="user-form">
        <?php
        $form = ActiveForm::begin([
            'id' => 'useredit-form',
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
//        'validationUrl' => ['validate', 'id'=>$model->us_id],
            'validateOnSubmit' => true,
            'validateOnChange' => false,
            'validateOnBlur' => false,
            'validateOnType' => false,
//    'options' => [
//        'action' => $_SERVER['REQUEST_URI'],
//    ],
            /* ********************** bootstrap options ********************** */
            /*
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                        'horizontalCssClasses' => [
                            'label' => 'col-sm-4',
                            'offset' => 'col-sm-offset-4',
                            'wrapper' => 'col-sm-8',
                            'error' => '',
                            'hint' => '',
                        ],
                    ],
            */
        ]);
        //        TC000206
$model = new Notificateact();
$aLab = $model->attributeLabels();
?>

<div class="col-sm-1" style="text-align: center;">
    <strong><?= $aLab['ntfd_message_age'] ?></strong><br />( дней )
</div>

<div class="col-sm-3" style="text-align: center;">
    <strong><?= $aLab['ntfd_operate'] ?></strong>
</div>

<div class="col-sm-1"></div>

<div class="clearfix"></div>

<?php
        echo MultirowsWidget::widget(
            [
                'model' => Notificateact::className(),
                'form' => $form,
                'records' => $aActions,
                'rowview' => '@app/views/notificateact/_formlist.php',
                'tagOptions' => ['class' => 'userdata-row'],
                'defaultattributes' => [],
                'addlinkselector' => $aAddNewRowId,
                'dellinkselector' => '.remove-action',
                'afterInsert' => 'function(ob){
            }',
            'afterDelete' => 'function(){ console.log("Delete row : resource"); }',
            'canDeleteLastRow' => true,
//                'script' => $sScript,
            ]
        );
        ?>

        <div class="clearfix"></div>
        <div class="" style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #cccccc;">
            <div class="col-sm-4">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Отмена', '', ['class' => 'btn btn-default', 'id' => "{$form->options['id']}-cancel"]) ?>
            </div>
            <div class="col-sm-1">
                <?= Html::a(
                        '<span class="glyphicon glyphicon-plus"></span>',
                        '',
                        [
                            'class' => 'btn btn-default',
                            'id' => substr($aAddNewRowId, 1),
                            'title' => 'Добавить действие',
                        ]
                ) ?>
            </div>
            <div class="col-sm-5">
                <div class="alert alert-success" role="alert" id="formresultarea" style="display: none; text-align: center;"></div>
            </div>


            <div class="clearfix"></div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php

$sJs = <<<EOT
var oForm = jQuery('#{$form->options['id']}'),
    oCancel = jQuery('#{$form->options['id']}-cancel'),
    oDialog = oForm.parents('[role="dialog"]');

oCancel.on(
    "click",
    function(event){
        event.preventDefault();
        if( oDialog.length > 0 ) {
            oDialog.modal('hide');
        }
        else {
            window.history.go(-1);
        }
        return false; });

oForm
// .on('beforeSubmit', function(e) {
// })
// .on('afterValidate', function (event, messages) {
//    console.log("afterValidate()", event);
//    console.log(messages);
//    if( "result" in messages ) {
//    }
// })
.on('submit', function (event) {
//    console.log("submit()");
    var formdata = oForm.data().yiiActiveForm,
        oRes = jQuery("#formresultarea");

    event.preventDefault();
    if( formdata.validated ) {
        // имитация отправки
        formdata.validated = false;
        formdata.submitting = true;

        // показываем подтверждение
        oRes
            .text("Данные сохранены")
            .fadeIn(800, function(){
                setTimeout(
                    function(){
                        oRes.fadeOut(function(){ window.location.reload(); });
                    },
                    1000
                );
            });
    }
    return false;
});
//console.log("oForm = ", oForm);
EOT;

$this->registerJs($sJs, View::POS_READY, 'submit_user_form');