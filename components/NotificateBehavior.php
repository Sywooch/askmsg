<?php
/**
 * User: KozminVA
 * Date: 10.03.2015
 * Time: 15:23
 */

namespace app\components;

use Closure;
use yii\base\Behavior;
use yii\base\Event;


class NotificateBehavior extends Behavior {
    /**
     * @var array список событий, по которым проверять
     *
     * ```php
     * [
     *     ActiveRecord::EVENT_BEFORE_INSERT,
     *     ActiveRecord::EVENT_BEFORE_UPDATE,
     * ]
     * ```
     */
    public $allevents = [];

    /**
     * @var mixed функция для выполнения
     *
     * ```php
     * function ($event, $model)
     * {
     * }
     * ```
     */
    public $value;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return array_fill_keys($this->allevents, 'testFields');
    }

    /**
     * Выполняем проверку на необходимость извещения
     * @param Event $event
     */
    public function testFields($event)
    {
        if( in_array($event->name, $this->allevents) ) {
            $this->getValue($event, $this->owner);
        }
    }

    /**
     * @param Event $event
     */
    protected function getValue($event, $model)
    {
        $this->value instanceof Closure ? call_user_func($this->value, $event, $model) : '';
    }
}
