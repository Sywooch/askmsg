<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Vic
 */
class ListdataAsset extends AssetBundle
{
    public $sourcePath = '@app/static';
    public $css = [
        'css/list.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\grid\GridViewAsset',
    ];
    public $publishOptions = [
        'forceCopy' => YII_ENV_DEV,
    ];
}
