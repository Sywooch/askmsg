<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'us_id',
            'us_xtime',
            'us_login',
            'us_password_hash',
            'us_chekword_hash',
            // 'us_active',
            // 'us_name',
            // 'us_secondname',
            // 'us_lastname',
            // 'us_email:email',
            // 'us_logintime',
            // 'us_regtime',
            // 'us_workposition',
            // 'us_checkwordtime',
            // 'auth_key',
            // 'email_confirm_token:email',
            // 'password_reset_token',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
