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
     * @var string шаблон письма о пароле
     */
    public $template = '';

    /**
     * @var string тема письма о пароле
     */
    public $subject = '';

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
            /** @var User $model */
            $model = $this->owner;

            if( $model->newPassword == '' ) {
                $sPassword = substr(str_replace(['_', '-'], ['', ''], Yii::$app->security->generateRandomString()), 0, 8);
            }
            else {
                $sPassword = $model->newPassword;
            }
            $model->newPassword = $sPassword;
            Yii::error('setNewPassword(): new password = ' . $sPassword);

            $model->setPassword($sPassword);
            $model->generateAuthKey();
            $template = empty($this->template) ? 'user_create_info' : $this->template;
            $subject = empty($this->subject) ? ('Уведомление портала ' . Yii::$app->name) : $this->subject;
            $model->sendNotificate($template, $subject, ['model' => $model] );
        }
    }

}