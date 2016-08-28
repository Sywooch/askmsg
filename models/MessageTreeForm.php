<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class MessageTreeForm extends Model
{
    public $msg_pers_text;
    public $msg_file;
    public $is_satisfied;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['msg_pers_text'], 'required'],
            [['msg_pers_text'], 'string', 'min' => 100, ],
            [['is_satisfied'], 'integer', ],
//            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'msg_pers_text' => 'Сообщение',
            'msg_file' => 'Файл',
            'is_satisfied' => 'Удовлетворен',
        ];
    }
}
