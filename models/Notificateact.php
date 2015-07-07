<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%notificateact}}".
 *
 * @property integer $ntfd_id
 * @property integer $ntfd_message_age
 * @property integer $ntfd_operate
 * @property integer $ntfd_flag
 */
class Notificateact extends \yii\db\ActiveRecord
{
    const ACTI_EMAIL_EPLOEE = 1;
    const ACTI_EMAIL_CONTROLER = 2;
    const ACTI_EMAIL_MODERATOR = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notificateact}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ntfd_message_age', 'ntfd_operate'], 'required'],
            [['ntfd_operate'], 'in', 'range' => array_keys($this->acts)],

            [['ntfd_message_age', 'ntfd_operate', 'ntfd_flag'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ntfd_id' => 'id',
            'ntfd_message_age' => 'Срок от создания',
            'ntfd_operate' => 'Действие',
            'ntfd_flag' => 'Дополнительные флаги',
        ];
    }

    public function getActs() {
        return [
            self::ACTI_EMAIL_EPLOEE => 'Отправить email исполнителю',
            self::ACTI_EMAIL_CONTROLER => 'Отправить email контролеру',
            self::ACTI_EMAIL_MODERATOR => 'Отправить email модератору',
        ];
    }
}
