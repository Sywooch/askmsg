<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppvideoAsset;
use app\components\widgets\Alert;
use app\models\Rolesimport;
use yii\web\View;

/* @var $this \yii\web\View */
/* @var $content string */

/*
.container-fluid {
    min-width: 1200px; - отключить
}

#alf {
    min-width: 1200px; - отключить
}

#top_logo_block - class="col-xs-12"
#top_menu_block - class="col-xs-12"

#head ul.menu li.active {
    background-image: url("../images/beak.png"); - отключить
}

<span class="logo-box"> - в top_logo_block display: none;

id="site" padding-left: 0, padding-right: 0,

*/
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

if( $isAdmin || $isModerate ) {
    $aMenuItems[] = [
        'label' => ' ', // <span class="glyphicon glyphicon-triangle-bottom"></span>
//        'url' => ['message/moderatelist'],
        'items' => [
            ['label' => 'Список оповещений', 'url' => ['notificateact/index'], 'options' => ['class' => 'nocommonclass'], ],
            ['label' => 'Оповестить исполнителей', 'url' => ['notificateact/process'], 'options' => ['class' => 'nocommonclass'], ],
            ['label' => 'Перенаправления тем', 'url' => ['subjredirect/index'], 'options' => ['class' => 'nocommonclass'], ],
            ['label' => 'Теги', 'url' => ['tags/index', 'TagsSearch[tag_type]' => 1, 'TagsSearch[tag_active]' => 1,], 'options' => ['class' => 'nocommonclass', 'style' => 'width: 80%;', ]],
            ['label' => 'Выгрузка', 'url' => ['message/exportdata'], 'options' => ['class' => 'nocommonclass'], ],
//            '<li class="divider"></li>',
//            '<li class="dropdown-header">Dropdown Header</li>',
//            ['label' => 'Level 1 - Dropdown B', 'url' => '#'],
        ],
    ];
}
/*
<li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#">Separated link</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#">One more separated link</a></li>
          </ul>
        </li>
*/
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
    <div class="row minheight" id="info-page-block">
        <div class="col-xs-12 minheight">
            <div id="alf" class="container-fluid shadow">
                <div class="row underline">
                    <div class="col-xs-12">
                        <div class="row alf-margin-box">
                            <div class="col-xs-12">
                                <div id="head" class="row">
                                    <div class="col-xs-5" id="top_logo_block">
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
                                    <div class="col-xs-7" id="top_menu_block">
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
    <div id="footer" style="width: 100%; max-width: 100%;">
        <div class="container-fluid" style="max-width: 1300px;">

            <div class="col-xs-6">
                <span class="text-url">&copy; <?= date('Y') ?></span> <a class="text-url mr" href="http://dogm.mos.ru">Департамент образования города Москвы</a>
                <span class="text-url">&copy; <?= date('Y') ?> Разработка и поддержка </span><a class="text-url" href="http://temocenter.ru">ТемоЦентр</a>
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
/*
.container-fluid {
    min-width: 1200px; - отключить
}

#alf {
    min-width: 1200px; - отключить
}

#top_logo_block - class="col-xs-12"
#top_menu_block - class="col-xs-12"

#head ul.menu li.active {
    background-image: url("../images/beak.png"); - отключить
}

<span class="logo-box"> - в top_logo_block display: none;

id="site" padding-left: 0, padding-right: 0,

.alf-margin-box {
    margin-left: 60px;
    margin-right: 60px;
}

#head {
    height: 79px;
}
*/
/*
// Это кусок для изменения стилей для мобильных телефонов
$sJs = <<<EOT
// if( /Android|webOS|Mobi|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
console.log("Test: " + jQuery(window).width());
if( jQuery(window).width() < 600 ) {
    jQuery(".container-fluid").attr("style", 'min-width : auto');
    jQuery("#alf").attr("style", 'min-width : auto');

    jQuery("#top_logo_block").addClass("col-xs-12").removeClass("col-xs-5");
    jQuery("#top_menu_block").addClass("col-xs-12").removeClass("col-xs-7");

    jQuery("#top_logo_block .logo-box").hide();

    jQuery("#site").css({'padding-left': 0, 'padding-right': 0});
    jQuery(".alf-margin-box").css({"margin-left": "10px", "margin-right": "10px"});

    jQuery("#info-page-block").css({"margin-left": 0, "margin-right": 0});

    jQuery(".message-index h1").css({clear: "both"});

    jQuery("#head").css({height: "auto"});
    jQuery("#head ul.menu li.active").css({"background-image": "none"});
}
EOT;

$this->registerJs($sJs, View::POS_READY, 'mobilecorrectjs');
*/

if( Yii::$app->user->isGuest ) {
?>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function (d, w, c) {
            (w[c] = w[c] || []).push(function () {
                try {
                    w.yaCounter29788254 = new Ya.Metrika({
                        id: 29788254,
                        clickmap: true,
                        trackLinks: true,
                        accurateTrackBounce: true
                    });
                } catch (e) {
                }
            });

            var n = d.getElementsByTagName("script")[0],
                s = d.createElement("script"),
                f = function () {
                    n.parentNode.insertBefore(s, n);
                };
            s.type = "text/javascript";
            s.async = true;
            s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f, false);
            } else {
                f();
            }
        })(document, window, "yandex_metrika_callbacks");
    </script>
    <noscript>
        <div><img src="//mc.yandex.ru/watch/29788254" style="position:absolute; left:-9999px;" alt=""/></div>
    </noscript>
    <!-- /Yandex.Metrika counter -->
<?php

}

?>
</body>
</html>
<?php $this->endPage() ?>
