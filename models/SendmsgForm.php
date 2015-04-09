<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * SendmsgForm is the model behind the send message
 */
class SendmsgForm extends Model
{
    public $id;
    public $uid;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['id', 'uid'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Сообщение',
            'uid' => 'Пользователь',
        ];
    }

}
