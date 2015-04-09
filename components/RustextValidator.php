<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 09.04.2015
 * Time: 15:13
 */

namespace app\components;

use Yii;
use yii\validators\Validator;

class RustextValidator extends Validator {
    public $russian = 1; // процент русских букв в тексте
    public $capital = 0.08; // процент заглавных букв в тексте
    public $usecapital = true; // проверять заглавные буквы

    public function validateAttribute($model, $attribute) {
        $sBaseVal = $model->$attribute;
        $sNewVal = str_replace(["\r", "\n"], ['', ''], $sBaseVal);
        $sNewVal = preg_replace('|[\\s]|', '', $sNewVal);
        $nBaseLen = mb_strlen($sNewVal, 'UTF-8');
//        Yii::info("RustextValidator(): base: {$nBaseLen}: {$sNewVal}");
        $sNewVal = preg_replace('|[A-za-z]|', '', $sNewVal);
        $nRusLen = mb_strlen($sNewVal, 'UTF-8');
//        Yii::info("RustextValidator(): rus: {$nRusLen}: {$sNewVal}");
        $sNewVal = preg_replace('|[А-ЯЁ]|u', '', $sNewVal);
//        Yii::info("RustextValidator(): small: {$nRusLen}: {$sNewVal}");
        $nCapitalLen = $nRusLen - mb_strlen($sNewVal, 'UTF-8');
//        Yii::info("RustextValidator(): {$sBaseVal} -> {$sNewVal}");
//        Yii::info("RustextValidator(): nBaseLen = {$nBaseLen}, nRusLen = {$nRusLen}, nCapitalLen = {$nCapitalLen}");
        if( $this->usecapital ) {
            if( ($nCapitalLen / $nRusLen) > $this->capital ) {
//                Yii::info("RustextValidator(): capital = " . sprintf("%.3f", $nCapitalLen / $nRusLen));
                $model->addError($attribute, 'Слишком много заглавных букв.');
            }
        }
        if( ($nRusLen / $nBaseLen) < $this->russian ) {
//            Yii::info("RustextValidator(): russian = " . sprintf("%.3f", ($nRusLen / $nBaseLen)));
            $model->addError($attribute, 'Необходимо написать побольше слов на русском языке.');
        }

    }

}