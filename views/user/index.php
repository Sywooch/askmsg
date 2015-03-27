<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\assets\GriddataAsset;
use app\models\User;
use app\models\Group;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;

GriddataAsset::register($this);

$aGroups = Group::getActiveGroups();

?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать нового пользователя', ['create'], ['class' => 'btn btn-success']) ?>
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
//            'us_name',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'us_name',
                'content' => function ($model, $key, $index, $column) {
                    /** @var User $model */
                    return Html::encode($model->getFullName()) . '<span>' . Html::encode($model->us_workposition) . '</span>';
                },
                'contentOptions' => [
                    'class' => 'griddate',
                ],
            ],
            // 'us_secondname',
            // 'us_lastname',
            // 'us_email:email',
            // 'us_logintime',
            // 'us_regtime',
//            'us_workposition',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'selectedGroups',
                'filter' => $aGroups,
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

            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['class' => 'commandcell'],
            ],
        ],
    ]); ?>

</div>
