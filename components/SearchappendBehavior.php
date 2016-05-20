<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 30.12.2015
 * Time: 11:56
 */

namespace app\components;

use yii;
use yii\base\Behavior;
use yii\web\UploadedFile;
use yii\base\InvalidConfigException;
use yii\base\InvalidValueException;

class SearchappendBehavior extends Behavior {
    /**
     * @var string form name for owner model
     */
    public $formname = null;

    /**
     *
     */
//    public function init() {
//        parent::init();
//    }

    /**
     * Получаем имя формы
     * @return array
     */
    public function getOwnerFormName() {
        return ( $this->formname !== null ) ? $this->formname : $this->owner->formName();
    }

    /**
     * @param string $sForm
     * @param string $sName
     * @param string $sVal
     * @param bool $bArray
     * @return array
     */
    public function getFormAttributeData($sForm, $sName, $sVal, $bArray = false) {
        if( $sForm == '' ) {
//            Yii::info('getFormAttributeData(): ' . print_r([rawurlencode($sName).($bArray ? '[]' : ''), rawurlencode($sVal)], true));
            return [$sName.($bArray ? '[]' : ''), $sVal];
        }
        else {
//            Yii::info('getFormAttributeData(): ' . print_r([$sForm . '['.rawurlencode($sName).']'.($bArray ? '[]' : ''), rawurlencode($sVal)], true));
            return [$sForm . '['.$sName.']'.($bArray ? '[]' : ''), $sVal];
        }
    }

    /**
     * Получаем непустые аттрибуты
     * @return array
     */
    public function getSearchAttributes() {
        $model = $this->owner;
        $aAttr = $model->safeAttributes();
        $aRet = [];
        foreach($aAttr As $v) {
            if( empty($model->$v) ) {
                continue;
            }
            // $aRet[$sForm . '['.$v.']'] = $this->$v;
            $aRet[$v] = $model->$v;
        }
        return $aRet;
    }

    /**
     * Получаем строку запроса в url
     * @return string
     */
    public function getSearchQuery() {
        $aAttr = $this->getSearchAttributes();
        $sForm = $this->getOwnerFormName();
        $aRet = [];
        foreach($aAttr As $k=>$v) {
            if( is_array($v) ) {
                foreach($v As $v1) {
                    $aRet[] = implode('=', $this->getFormAttributeData($sForm, $k, rawurlencode($v1), true));
//                    $aRet[] = $sForm . '['.rawurlencode($k).'][]=' . rawurlencode($v1);
                }
            }
            else {
                $aRet[] = implode('=', $this->getFormAttributeData($sForm, $k, rawurlencode($v), false));
//                $aRet[] = $sForm . '['.rawurlencode($k).']=' . rawurlencode($v);
            }
        }
        return implode('&', $aRet);
    }

    /**
     * Получаем параметры запроса
     * @return string
     */
    public function getSearchArray() {
        $aAttr = $this->getSearchAttributes();
        Yii::info('getSearchArray(): ' . print_r($aAttr, true));
        $sForm = $this->getOwnerFormName();
        $aRet = [];
        foreach($aAttr As $k=>$v) {
            if( is_array($v) ) {
                foreach($v As $v1) {
                    list($sn, $sv) = $this->getFormAttributeData($sForm, $k, $v1, true);
                    $aRet[$sn] = $sv;
//                    if( $sForm == '' ) {
//                        $aRet[$k.'[]'] = rawurlencode($v1);
//                    }
//                    else {
//                        $aRet[$sForm . '['.$k.'][]'] = rawurlencode($v1);
//                    }
                }
            }
            else {
                list($sn, $sv) = $this->getFormAttributeData($sForm, $k, $v, false);
                $aRet[$sn] = $sv;
                Yii::info('getSearchArray(): $k = '.$k.', $v = ' . $v . ', $sn = ' . $sn . ', $sv = ' . $sv);
//                if( $sForm == '' ) {
//                    $aRet[$k] = rawurlencode($v);
//                }
//                else {
//                    $aRet[$sForm . '['.$k.']'] = rawurlencode($v);
//                }
            }
        }
        return $aRet;
    }

}