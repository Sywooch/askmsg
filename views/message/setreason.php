<?php

use yii\helpers\Html;
use app\components\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Message */

$this->title = 'Отметка об обоснованности обращения';
$this->params['breadcrumbs'] = [];

?>
<div class="message-setreason">
    <div class="message-setreason-form">
        <div class="alert alert-warning">
            Укажите обоснованность обращения на сайте. В противном случае обращение останется в списке немодерированных.
        </div>

        <div class="row">
            <div class="col-sm-4">
                <div style="border: 1px solid #cccccc; border-radius: 3px; padding: 15px; margin: 15px 0;">
                    <?php $form = ActiveForm::begin([
                        'id' => 'message-setreason-form',
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => false,
                        'validateOnType' => false,
                        'validateOnChange' => false,
                        'validateOnBlur' => false,
                        'validateOnSubmit' => true,
//            'layout' => 'horizontal',
                        'options'=>[
                            'enctype'=>'multipart/form-data'
                        ],
//            'fieldConfig' => [
////                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
//                'horizontalCssClasses' => [
//                    'label' => 'col-sm-3',
//                    'offset' => 'col-sm-offset-3',
//                    'wrapper' => 'col-sm-9',
////                    'error' => '',
//                    'hint' => 'col-sm-9 col-sm-offset-3',
//                ],
//            ],
                    ]);
                    ?>

                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form
                                ->field($model, 'reasonable')
                                ->radioList($model->getAllReasons())
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-9">
                            <?= Html::submitButton('Установить', ['class' => 'btn btn-success btn-block']) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <div class="col-sm-8">
                <div style="border: 1px solid #cccccc; border-radius: 3px; padding: 0 15px 15px; margin: 15px 0;">
                    <h3>Текст обращения</h3>
                    <?= $model->msg_pers_text ?>
                    <h3>Текст ответа</h3>
                    <?= $model->msg_answer ?>
                </div>
            </div>
        </div>




    </div>
</div>
