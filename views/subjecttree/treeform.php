<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SubjectTree */
/* @var $formmodel app\models\MessageTreeForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $parents array of app\models\SubjectTree */
/* @var $child array of app\models\SubjectTree */

//$this->title = $model->subj_id;
//$this->params['breadcrumbs'][] = ['label' => 'Subject Trees', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;

?>
<div class="subject-tree-view">

    <?php
    $n = 0;
    $nPadd = 40;
    $aPrt = array_reduce(
        $parents,
        function($carry, $el) {
            $carry[] = Html::encode($el->subj_variant);
            return $carry;
        },
        []
    );

    foreach($aPrt As $sLink) { ?>
    <p style="padding-left: <?= $nPadd * $n; ?>px;">
        <strong><?= $sLink ?></strong>
    </p>
    <?php
        $n++;
    }

//    echo 'Parents: ' . implode('/', $aPrt);
    ?>

    <p style="padding-left: <?= $nPadd * $n; ?>px;">
        <strong><?= ($model === null) ? '' : Html::encode($model->subj_variant) ?></strong>
    </p>


    <?php
    if( empty($child) ) {
//            echo "Показываем форму";
        echo $form->field($formmodel, 'subject_id', ['template' => '{input}'])->hiddenInput() ;
        if( $formmodel->subject_id > 0 ) {
            echo $form->field($formmodel, 'is_user_variant', ['template' => '{input}'])->hiddenInput() ;
        }

        echo $this->render(
            '_treeinfotext',
            [
                'form' => $form,
                'model' => $model,
                'formmodel' => $formmodel,
            ]
        );
    }
    else {
        $aOptions = array_reduce(
            $child,
            function ($carry, $el) {
                if (!empty($el->subj_variant)) {
                    $carry[$el->subj_id] = $el->subj_variant;
                }
                return $carry;
            },
            []
        );
        ?>
        <div class="row">
            <div class="col-sm-12" style="padding-left: <?= $nPadd * $n; ?>px;">
                <?= $form
                    ->field($formmodel, 'subject_id', ['template' => '{input}'])
                    ->radioList(
                        $aOptions,
                        [
                            'separator' => '<br />',
                            'itemOptions' => [
                                'class' => 'radiobutton',
                            ],
                        ]) ?>
            </div>
        </div>

        <?php
        if( $formmodel->subject_id > 0 ) {
        ?>
            <div class="row">
                <div class="col-sm-12" style="padding-left: <?= $nPadd * $n; ?>px;">
                    <?= $form
                        ->field($formmodel, 'is_user_variant', ['template' => '{input}{error}'])
                        ->radioList(
                            [1 => 'Иное',],
                            [
                                'separator' => '<br />',
                                'itemOptions' => [
                                    'class' => 'otherradiobutton',
                                ],
                            ]) ?>
                </div>
            </div>
        <?php
        }
        ?>

    <?php
    }
    ?>
</div>
