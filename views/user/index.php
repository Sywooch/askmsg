<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\assets\GriddataAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;

GriddataAsset::register($this);

?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'us_id',
  //          'us_xtime',
            'us_login',
  //          'us_password_hash',
  //          'us_chekword_hash',
            // 'us_active',
            'us_name',
            // 'us_secondname',
            // 'us_lastname',
            // 'us_email:email',
            // 'us_logintime',
            // 'us_regtime',
//            'us_workposition',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'selectedGroups',
                'content' => function ($model, $key, $index, $column) {
                        $sOut = ''; // 'Permissions [' . count($model->permissions) . ']:';
                        foreach($model->permissions As $ob) {
                            $sOut .= '<span>' . $ob->group_name . '</span>';
                        }
                        return $sOut;
                    },
                'contentOptions' => [
                    'class' => 'griddate',
                ],
            ],

            // 'us_checkwordtime',
            // 'auth_key',
            // 'email_confirm_token:email',
            // 'password_reset_token',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
