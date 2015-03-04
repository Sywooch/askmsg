<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\components\widgets\Alert;
use app\models\Rolesimport;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
<?php

$isAdmin = \Yii::$app->user->can(Rolesimport::ROLE_ADMIN);
$isModerate = \Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM);
$isAnswer = \Yii::$app->user->can(Rolesimport::ROLE_ANSWER_DOGM);
$isGuest = \Yii::$app->user->isGuest;

$aMenuItems = [
    ['label' => 'Главная', 'url' => '/'],
];

$aMenuItems[] = ['label' => 'Обращения', 'url' => ['message/index']];

if( $isAdmin || $isModerate ) {
    $aMenuItems[] = ['label' => 'Модерировать', 'url' => ['message/moderatelist']];
}

if( $isAdmin || $isAnswer ) {
    $aMenuItems[] = ['label' => 'Отвечать', 'url' => ['message/answerlist']];
}

$aMenuItems[] = Yii::$app->user->isGuest ?
    ['label' => 'Вход', 'url' => ['/site/login']] :
    ['label' => 'Выход (' . Yii::$app->user->identity->us_login . ')',
        'url' => ['/site/logout'],
        'linkOptions' => ['data-method' => 'post']
    ];

            NavBar::begin([
                'brandLabel' => 'Вопросы/ответы',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $aMenuItems,
            ]);
            NavBar::end();
        ?>

        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget(); ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; My Company <?= date('Y') ?></p>
            <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
