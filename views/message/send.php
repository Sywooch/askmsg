<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Отправить сообщение по email';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'sendmessage-form',
        'options' => ['class' => 'form-horizontal'],
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validateOnBlur' => false,
        'validateOnChange' => false,
        'validateOnType' => false,
        'validateOnSubmit' => true,
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-6\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-3 control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'uid') ?>

    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-8">
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end();
    ?>

    <div class="clearfix"></div>

</div>

<?php
$sJs = <<<EOT
var oForm = jQuery('#sendmessage-form');
console.log("Add after validation function", oForm);
oForm.on('afterValidate', function (event, message) {
    console.log("After validate");
    return false;
});

oForm.on('beforeSubmit', function (event) {
    console.log("beforeSubmit");
    return false;
});


EOT;

$this->registerJs($sJs, View::POS_READY, 'aftervalidate');