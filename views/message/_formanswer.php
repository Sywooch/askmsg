<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\web\View;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Regions;
use yii\widgets\MaskedInput;
use app\assets\ListdataAsset;
use yii\bootstrap\Modal;


/* @var $this yii\web\View */
/* @var $model app\models\Message */
/* @var $form yii\widgets\ActiveForm */

ListdataAsset::register($this);

?>

<div class="message-form">
    <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-sm-2',
                    'offset' => 'col-sm-offset-2',
                    'wrapper' => 'col-sm-10',
                ],
            ],
    ]);

    ?>



    <?= $form->field(
            $model,
            'msg_answer')
        ->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <label for="message-msg_pers_text" class="control-label col-sm-2">&nbsp;</label>
        <div class="col-sm-6">
            <?= Html::submitButton('Ответить', ['class' => 'btn btn-primary']) ?>
        </div>
        <div class="control-label col-sm-4">
            <?php
                // показываем кнопу для вывода обращения
                echo Html::a('Текст обращения', '#', ['class' => 'btn btn-success', 'id'=>'idshowmessage']);
                $this->registerJs('jQuery("#idshowmessage").on("click", function(event) { event.preventDefault(); $("#messagedata").modal("show"); return false; });', View::POS_READY, 'myKey');
            ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>


    <?php
        // Окно для обращения
        Modal::begin([
            'header' => 'Обращение № ' . $model->msg_id,
            'id' => 'messagedata',
        ]);
    /*        'toggleButton' => [
                'label' => 'Текст обращения',
                'class' => 'btn btn-success',
            ],
    */
    ?>

    <?=
    $this->render(
        '_view',
        [
            'model' => $model,
        ]
    )
    ?>

    <?php
    Modal::end();
    ?>


</div>
