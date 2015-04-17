<?php
/**
 * User: KozminVA
 * Date: 17.04.2015
 * Time: 13:35
 */

namespace app\components;

use Yii;

trait WaitTrait {

    /**
     * @param string $sName имя параметра с задержкой
     */
    public function DoDelay($sName) {
        if( isset(Yii::$app->params[$sName]) && (Yii::$app->params[$sName] > 0)) {
            usleep(Yii::$app->params[$sName]);
        }
    }

}