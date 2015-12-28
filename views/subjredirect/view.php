<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Subjredirect */

$this->title = $model->redir_adress;
$this->params['breadcrumbs'][] = ['label' => 'Перенаправления', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subjredirect-view">

    <!-- h1><?= '' // Html::encode($this->title) ?></h1 -->

<!--    <p>-->
<!--        --><?php //= Html::a('Update', ['update', 'id' => $model->redir_id], ['class' => 'btn btn-primary']) ?>
<!--        --><?php //= Html::a('Delete', ['delete', 'id' => $model->redir_id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => 'Are you sure you want to delete this item?',
//                'method' => 'post',
//            ],
//        ]) ?>
<!--    </p>-->

        <p>
            <?php echo Html::a('Список перенаправлений', ['index', ], ['class' => 'btn btn-success']) ?>
        </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'redir_id',
//            'redir_tag_id',
            [
                'attribute' => 'redir_tag_id',
                'value' => $model->subject ? $model->subject->tag_title : '',
            ],
            'redir_adress',
            'redir_description',
        ],
    ]) ?>

</div>
