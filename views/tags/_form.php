<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use app\models\Tags;

/* @var $this yii\web\View */
/* @var $model app\models\Tags */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="tags-form">

    <?php $form = ActiveForm::begin([
            'id' => 'tags-form',
            'layout' => 'horizontal',
            'fieldConfig' => [
//                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                'horizontalCssClasses' => [
                    'label' => 'col-sm-3',
                    'offset' => 'col-sm-offset-3',
                    'wrapper' => 'col-sm-3',
//                    'error' => '',
//                    'hint' => '',
                ],
            ],
        ]); ?>

    <?= $form->field($model, 'tag_title')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'tag_active')->checkbox([], false) ?>

    <?= $form->field($model, 'tag_type')->dropDownList(Tags::$_aTypes) ?>

    <?= $form->field($model, 'tag_rating_val')->checkbox([], false) ?>

    <div class="form-group">
        <label for="message-msg_pers_text" class="control-label col-sm-3">&nbsp;</label>
        <div class="col-sm-3">
            <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
