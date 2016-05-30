<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Stateflag;

/**
 *
 */
class Test1Controller extends Controller
{
    /**
     * @param string $message the message to be echoed.
     */
    public function actionIndex()
    {
        $aStates = ['Answer', 'Appeal'];
        $aNames = [
            'Flags' => null,
            'Flag' => [
                'Answer' => [0, 1, 2, 3, 4, 5],
                'Appeal' => [0, 1, 2, 3,],
            ],
            'Color' => [
                'Answer' => [0, 1, 2, 3, 4, 5],
                'Appeal' => [0, 1, 2, 3,],
            ],
            'Glyth' => [
                'Answer' => [0, 1, 2, 3, 4, 5],
                'Appeal' => [0, 1, 2, 3,],
            ],
            'Hint' => [
                'Answer' => [0, 1, 2, 3, 4, 5],
                'Appeal' => [0, 1, 2, 3,],
            ],
        ];

        foreach($aStates As $sType) {
            foreach($aNames As $sFunc => $args) {
                $sFuncName = 'get' . $sType . $sFunc;
                if( $args === null ) {
                    echo "{$sFuncName}()\n";
                    $vars = get_class_vars('app\\models\\Stateflag');
                    $this->runStateFunction($sFuncName, null, $vars['a'.$sType.'Flags']);
                    echo "\n";
                }
                else {
                    $vars = get_class_vars('app\\models\\Stateflag');

                    if( isset($args[$sType]) ) {
                        foreach($args[$sType] As $sArg) {
                            $sExp = ($sFunc == 'Flag') ? $vars['a'.$sType.'Flags'][$sArg] : $vars['a'.$sType.'Flags'][$sArg][strtolower($sFunc)];
                            echo "{$sFuncName}({$sArg}) ";
                            $this->runStateFunction($sFuncName, $sArg, $sExp);
                            echo "\n";
                        }
                    }
                    else {
                        foreach($args As $sArg) {
                            echo "{$sFuncName}({$sArg}) -1 ";
                            $this->runStateFunction($sFuncName, $sArg, -1);
                            echo "\n";
                        }
                    }
                }
            }
        }

//        $a = Stateflag::getAnswerFlags();
//        $b = Stateflag::$aAnswerFlags;
//
//        $aDif = array_diff(array_keys($a), array_keys($b));
//        echo count($aDif) > 0 ? ('Error more getAnswerFlags: id ' . implode(', ', $aDif)) : 'getAnswerFlags OK(1)' . "\n";
//
//        $aDif = array_diff(array_keys($b), array_keys($a));
//        echo count($aDif) > 0 ? ('Error less getAnswerFlags: id ' . implode(', ', $aDif)) : 'getAnswerFlags OK(2)' . "\n";
    }

    public function runStateFunction($sFunction, $sArg = null, $aResult) {
        if( $sArg === null ) {
            $aRet = call_user_func(array('app\\models\\Stateflag', $sFunction));
            if( is_array($aResult) ) {
                $aDif = array_diff(array_keys($aRet), array_keys($aResult));
                echo ' ' . (count($aDif) > 0 ? ('Error more '.$sFunction.': id ' . implode(', ', $aDif)) : ('OK(1)'));

                $aDif = array_diff(array_keys($aResult), array_keys($aRet));
                echo ' ' . (count($aDif) > 0 ? ('Error less '.$sFunction.': id ' . implode(', ', $aDif)) : ('OK(2)'));
            }
        }
        else {
            $aRet = call_user_func(array('app\\models\\Stateflag', $sFunction), $sArg);
            if( gettype($aResult) != gettype($aRet) ) {
                echo ' ' . 'Error '.$sFunction.' return ' . gettype($aRet) . ' but need ' . gettype($aResult);
            }
            else if( is_array($aResult) ) {
                $aDif = array_diff(array_keys($aRet), array_keys($aResult));
                echo ' ' . (count($aDif) > 0 ? ('Error more '.$sFunction.': id ' . implode(', ', $aDif)) : ('OK(1)'));

                $aDif = array_diff(array_keys($aResult), array_keys($aRet));
                echo ' ' . (count($aDif) > 0 ? ('Error less '.$sFunction.': id ' . implode(', ', $aDif)) : ('OK(2)'));
            }
            else {
                echo $aResult == $aRet ? ' OK' : (' Error '.$sFunction.' return ' . $aRet . ' expect ' . $aResult);
            }

        }
    }

}
