<?php

namespace app\assets;

use yii\web\AssetBundle;

class AppvideoAsset extends AssetBundle
{
//    public $basePath = '@webroot';
//    public $baseUrl = '@web';
    public $css = [
        'themes/fromvideo/css/bootstrap-theme.min.css',
        'themes/fromvideo/css/alf.css',
        'themes/fromvideo/css/site.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
