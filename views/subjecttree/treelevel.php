<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SubjectTree */
/* @var $parents array of app\models\SubjectTree */
/* @var $child array of app\models\SubjectTree */

//$this->title = $model->subj_id;
//$this->params['breadcrumbs'][] = ['label' => 'Subject Trees', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;

?>
<div class="subject-tree-view">
    <p>
    <?php
    $n = 0;
    $nPadd = 20;
    $aPrt = array_reduce(
        $parents,
        function($carry, $el) {
            $carry[] = Html::a($el->subj_variant, ['subjecttree/view', 'id' => $el->subj_id]);
            return $carry;
        },
        [Html::a('Темы', ['subjecttree/view', ])]
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
    </p>
    <p style="padding-left: <?= $nPadd * $n; ?>px;">
        <strong><?= ($model === null) ? '' : Html::encode($model->subj_variant) ?></strong>
    </p>

    <p>
        <?php
        if( empty($child) ) {
            echo "Показываем форму";
            echo $this->render(
                '_infotext',
                [
                    'model' => $model,
                ]
            );
        }
        else {
            $aPrt = array_reduce(
                $child,
                function($carry, $el) {
                    $carry[] = Html::a($el->subj_variant, ['subjecttree/view', 'id' => $el->subj_id]);
                    return $carry;
                },
                []
            );
            echo implode('<br />', $aPrt);
        }
        ?>
    </p>


</div>
