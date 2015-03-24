<?php
/**
 * User: KozminVA
 * Date: 24.03.2015
 * Time: 10:03
 */

namespace app\components;

use Yii;
use Closure;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\ActiveRecord;
use app\models\Message;

class ChangestateBehavior extends Behavior {

    /**
     * @var Closure Функция, которая будет выполнена при изменении состояния
     * Описание функции такое:
     *
     * ```php
     * function ($event)
     * {
     *     // что-то делаем
     * }
     * ```
     */
    public $value;

    /**
     * @var array таблица переходов,
     *
     * ключ - новый флаг состояния,
     * значение - массив старых состояний, при переходе из которых выполняем действие
     * если массив пустой, то выполняем действие
     */
    public $transTable = [];

    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'testStateFlag',
            ActiveRecord::EVENT_AFTER_INSERT => 'testStateFlag',
        ];
    }

    /**
     * @param Event $event событие
     */
    public function testStateFlag($event) {
        /** @var Message $model */
        $model = $event->sender;
        if( !isset($model->_oldAttributes['msg_flag']) ) {
            $model->_oldAttributes['msg_flag'] = 0;
        }

        $sOut = 'testStateFlag(' . $event->name . ') flags: ' . $model->_oldAttributes['msg_flag'] . ' -> ' . $model->msg_flag
        . ' this->transTable['.$model->msg_flag.']: ' . (isset($this->transTable[$model->msg_flag]) ? ('[' . implode(', ', $this->transTable[$model->msg_flag]) . ']') : '---');

        if( ($model->msg_flag != $model->_oldAttributes['msg_flag'])
         && isset($this->transTable[$model->msg_flag])
         && (empty($this->transTable[$model->msg_flag]) || in_array($model->_oldAttributes['msg_flag'], $this->transTable[$model->msg_flag])) ) {
            $sOut .= "testStateFlag(): run user func";
            call_user_func($this->value, $event);
        }
        else {
            $sOut .= "testStateFlag(): not run user func";
        }

        Yii::info($sOut);
    }

}