<?php
/**
 * User: KozminVA
 * Date: 16.03.2015
 * Time: 11:38
 */

namespace app\assets;

use yii\web\AssetBundle;


class JqueryfilerAsset extends AssetBundle {

    /**
     * @inheritdoc
     */
    public $sourcePath = '@vendor/creativedream/jqueryfiler';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/jquery.filer.min.js',
    ];

    /**
     * @inheritdoc
     */
    public $css = [
        'css/jquery.filer.css',
        'css/themes/jquery.filer-dragdropbox-theme.css',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
/*
    public function init()
    {
        parent::init();

//        $this->js[] = (YII_ENV_DEV ? 'js/jquery.filer.js' : 'js/jquery.filer.min.js');
        $this->js[] = 'js/jquery.filer.min.js';
        j.attr('style', '-webkit-box-shadow: ' +  h + '; -moz-box-shadow: ' +  h + '; box-shadow: ' +  h + ';')

    }
*/
}