<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppvideoAsset;
use app\components\widgets\Alert;
use app\models\Rolesimport;

/* @var $this \yii\web\View */
/* @var $content string */

AppvideoAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="<?= $this->theme->baseUrl . 'images/favicon.ico' ?>" rel="shortcut icon" type="image/vnd.microsoft.icon" />
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>

<?php

$isAdmin = \Yii::$app->user->can(Rolesimport::ROLE_ADMIN);
$isModerate = \Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM);
$isAnswer = \Yii::$app->user->can(Rolesimport::ROLE_ANSWER_DOGM);
$isGuest = \Yii::$app->user->isGuest;

$aMenuItems = [
    ['label' => 'Главная', 'url' => [Yii::$app->homeUrl], 'active' => Yii::$app->defaultRoute == Yii::$app->controller->getRoute()],
];

$aMenuItems[] = ['label' => 'Обращения', 'url' => ['message/index']];
if( $isAdmin || $isModerate ) {
    $aMenuItems[] = ['label' => 'Модерировать', 'url' => ['message/moderatelist']];
}

if( $isAdmin || $isAnswer ) {
    $aMenuItems[] = ['label' => 'Отвечать', 'url' => ['message/answerlist']];
}
/*
$aMenuItems[] = Yii::$app->user->isGuest ?
    ['label' => 'Вход', 'url' => ['/site/login']] :
    ['label' => 'Выход (' . Yii::$app->user->identity->us_login . ')',
        'url' => ['/site/logout'],
        'linkOptions' => ['data-method' => 'post']
    ];
*/
/*
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
*/        ?>

<div id="site" class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div id="alf" class="container-fluid shadow">
                <div class="row underline">
                    <div class="col-xs-12">
                        <div class="row alf-margin-box">
                            <div class="col-xs-12">
                                <div id="head" class="row">
                                    <div class="col-xs-5">
                                        <a href="<?= Yii::$app->homeUrl ?>" class="dogm-logo"></a>
                                <span class="logo-box" href="/">
                                    <div class="text">
                                        <div class="line1">
                                            Обращения
                                        </div>
                                        <div class="line2">
                                            Департамента образования города Москвы
                                        </div>
                                    </div>
                                </span>
                                    </div>
                                    <div class="col-xs-7">
                                        <div class="moduletable_menu">
                                            <?= Nav::widget([
                                                'options' => ['class' => 'nav menu'],
                                                'items' => $aMenuItems,
                                            ]);
                                            /*
                                            <!-- ul class="nav menu">
                                                <li class="item-126 current active"><a href="/" >Циклограмма</a></li>
                                                <li class="item-101 parent"><a href="/online.html" >Трансляции</a></li>
                                                <li class="item-102"><a href="/archive.html" >Видеоархив</a></li>
                                            </ul -->
                                             */
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="row">
                    <div class="col-xs-12">
                        <div class="row alf-margin-box">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-xs-12"></div>
                                </div>
                                <div id="system-message-container"></div>

                                <div class="row">
                                    <div id="circle" class="col-xs-12">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <br />
                                                <br />
                                                <?= Alert::widget(); ?>
                                                <?= $content ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>





            </div>

            <div id="footer" class="container-fluid">
                <div class="row">

                    <div class="col-xs-6">
                        <span class="text-url">&copy; 2015</span> <a class="text-url mr" href="http://dogm.mos.ru">Департамент образования города Москвы</a>
                        <span class="text-url">&copy; 2015 Разработка и поддержка </span><a class="text-url" href="http://temocenter.ru">ТемоЦентр</a>
                    </div>
                    <div class="col-xs-6" style="text-align: right;"><span class="text-url"></span>
                        <?=
                            Html::a(
                                Yii::$app->user->isGuest ? 'Вход' : 'Выход (' . Yii::$app->user->identity->us_login . ')',
                                Yii::$app->user->isGuest ? ['/site/login'] : ['/site/logout'],
                                Yii::$app->user->isGuest ? ['class' => 'text-url'] : ['class' => 'text-url', 'options' => ['data-method' => 'post']]
                            )
                        ?>

                    </div>
                </div>

            </div>

        </div></div>
</div>

<?php $this->endBody() ?>
<?php /*

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
            <p class="pull-left">&copy; Темоцентр</p>
            <p class="pull-right"><?= date('Y') ?></p>
        </div>
    </footer>

*/
?>
</body>
</html>
<?php $this->endPage() ?>
