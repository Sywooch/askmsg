<?php
/**
 * User: KozminVA
 * Date: 02.03.2015
 * Time: 11:59
 */

namespace app\components;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

use app\models\User;

/**
 * Class PurifierBehavior
 * Данное поведение очищает от небезопастного кода указаные в настройках атрибуты.
 *
 * Пример использования:
 * ```
 * ...
 * 'purifier' => [
 * 'class' => 'app\components\PasswordBehavior',
 * 'attributes' => [
 * ActiveRecord::EVENT_BEFORE_INSERT => true,
 * ],
 * ]
 * ...
 * ```
 *
 * @property string $attributes аттрибут с паролем
 */
class PasswordBehavior extends Behavior
{
    /**
     * @var string аттрибут с паролем
     */
    public $attributes = [];

    /**
     * Назначаем обработчик для [[owner]] событий.
     * @return array События (array keys) с назначеными им обработчиками (array values).
     */
    public function events()
    {
        foreach ($this->attributes as $i => $event) {
            $events[$i] = 'setNewPassword';
        }
        return $events;
    }

    /**
     * Создаем пароль
     * @param Event $event Текущее событие.
     */
    public function setNewPassword($event)
    {
        if ( isset($this->attributes[$event->name]) && $this->attributes[$event->name] ) {
            $sPassword = substr(str_replace(['_', '-'], ['', ''], Yii::$app->security->generateRandomString()), 0, 8);
            $this->owner->setPassword($sPassword);
            $this->owner->generateAuthKey();

            Yii::$app->mailer->compose('newUser', ['user' => $this->owner, 'password' => $sPassword])
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                ->setTo($this->owner->us_email)
                ->setSubject('Регистрация в приложении ' . Yii::$app->name)
                ->send();
        }
    }

}