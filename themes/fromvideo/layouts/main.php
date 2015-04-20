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
$sHost = $_SERVER['HTTP_HOST'];
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="<?= $this->theme->baseUrl . '/images/favicon.ico' ?>" rel="shortcut icon" type="image/vnd.microsoft.icon" />
    <link href="http://<?= $sHost ?>/apple-touch-icon.png" rel="apple-touch-icon" />
    <link href="http://<?= $sHost ?>/apple-touch-icon-76.png" rel="apple-touch-icon" sizes="76x76" />
    <link href="http://<?= $sHost ?>/apple-touch-icon-120.png" rel="apple-touch-icon" sizes="120x120" />
    <link href="http://<?= $sHost ?>/apple-touch-icon-152.png" rel="apple-touch-icon" sizes="152x152" />
    <link href="http://<?= $sHost ?>/apple-touch-icon-180.png" rel="apple-touch-icon" sizes="180x180" />
    <link href="http://<?= $sHost ?>/apple-touch-icon-precomposed.png" rel="apple-touch-icon-precomposed" />
    <link href="http://<?= $sHost ?>/apple-touch-icon-76-precomposed.png" rel="apple-touch-icon-precomposed" sizes="76x76" />
    <link href="http://<?= $sHost ?>/apple-touch-icon-120-precomposed.png" rel="apple-touch-icon-precomposed" sizes="120x120" />
    <link href="http://<?= $sHost ?>/apple-touch-icon-152-precomposed.png" rel="apple-touch-icon-precomposed" sizes="152x152" />
    <link href="http://<?= $sHost ?>/apple-touch-icon-180-precomposed.png" rel="apple-touch-icon-precomposed" sizes="180x180" />
    <link href="http://<?= $sHost ?>/icon-hires.png" rel="icon" sizes="192x192" />
    <link href="http://<?= $sHost ?>/icon-normal.png" rel="icon" sizes="128x128" />
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>

<?php

$isAdmin = \Yii::$app->user->can(Rolesimport::ROLE_ADMIN);
$isModerate = \Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM);
$isAnswer = \Yii::$app->user->can(Rolesimport::ROLE_ANSWER_DOGM);
$isGuest = \Yii::$app->user->isGuest;

$sLogoLink = 'http://dogm.mos.ru';

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
    <div class="row minheight">
        <div class="col-xs-12 minheight">
            <div id="alf" class="container-fluid shadow">
                <div class="row underline">
                    <div class="col-xs-12">
                        <div class="row alf-margin-box">
                            <div class="col-xs-12">
                                <div id="head" class="row">
                                    <div class="col-xs-5">
                                        <a href="<?= $sLogoLink /* Yii::$app->homeUrl */ ?>" class="dogm-logo" target="_blank"></a>
                                <span class="logo-box">
                                    <div class="text">
                                        <div class="box1">
                                            <a href="<?= $sLogoLink ?>" target="_blank" class="dogmlink">
                                            ДЕПАРТАМЕНТ ОБРАЗОВАНИЯ <span>ГОРОДА МОСКВЫ</span>
                                            </a>
                                        </div>
                                        <div class="box2">
                                            ОБРАЩЕНИЯ <span>К РУКОВОДИТЕЛЮ</span>
                                        </div>
<?php /*                                        <div class="line1">
                                            Обращения к руководителю
                                        </div>
                                        <div class="line2">
                                            Департамента образования города Москвы
                                        </div>
 */ ?>
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


        </div></div>
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
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter29788254 = new Ya.Metrika({id:29788254,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/29788254" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
</body>
</html>
<?php $this->endPage() ?>
