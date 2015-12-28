<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use app\models\Tags;

/* @var $this yii\web\View */
/* @var $model app\models\Subjredirect */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="subjredirect-form">

    <?php $form = ActiveForm::begin([
        'id' => 'redirect-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validateOnBlur' => false,
        'validateOnChange' => false,
        'validateOnType' => false,
        'validateOnSubmit' => true,
    ]); ?>

    <?= $form->field($model, 'redir_tag_id')->dropDownList(ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_SUBJECT), 'tag_id', 'tag_title'), []); //  ->textInput() ?>

    <?= $form->field($model, 'redir_adress')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'redir_description')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Изменить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
