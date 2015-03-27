<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$sTitle = $model->us_lastname . ' ' .$model->us_name;
$this->title = $sTitle;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->us_id], ['class' => 'btn btn-primary']) ?>
        <?php /* = Html::a('Удалить', ['delete', 'id' => $model->us_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить пользователя?',
                'method' => 'post',
            ],
        ]) */ ?>
        <?= Html::a('Вернуться к списку', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'us_id',
//            'us_xtime',
            'us_login',
//            'us_password_hash',
//            'us_chekword_hash',
            [
                'attribute' => 'us_active',
                'value' => $model->us_active ? 'Да' : 'Нет',
            ],
            [
                'attribute' => 'selectedGroups',
                'value' => implode(
                    ', ',
                    ArrayHelper::getColumn(
                        $model->permissions,
                        'group_name'
                    )
                ),

            ],
//            'us_active',
            'us_name',
            'us_secondname',
            'us_lastname',
            'us_email:email',
            'us_logintime',
            'us_regtime',
            'us_workposition',
//            'us_checkwordtime',
//            'auth_key',
//            'email_confirm_token:email',
//            'password_reset_token',
        ],
    ]) ?>

</div>
