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
    public $msg_pers_name;
    public $msg_pers_secname;
    public $msg_pers_lastname;
    public $msg_pers_email;
    public $msg_pers_phone;
    public $msg_pers_org;
    public $msg_pers_region;
    public $subject_id;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['msg_pers_text', 'msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', ], 'required'],
            [['msg_pers_text'], 'string', 'min' => 100, ],
            [['is_satisfied'], 'integer', ],

            [['msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', ], 'filter', 'filter' => 'trim'],
            [['msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', ], 'string', 'max' => 255],
            [['msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', ], 'match',
                'pattern' => '|^[А-Яа-яЁё]{2}[-А-Яа-яЁё\\s]*$|u', 'message' => 'Допустимы символы русского алфавита',
            ],
            [['msg_pers_name', ], 'filterUserName', 'on' => 'person', ],
            [['msg_pers_phone', ], 'match',
                'pattern' => '|^\\+7\\([\\d]{3}\\)\s+[\\d]{3}-[\\d]{2}-[\\d]{2}$|', 'message' => 'Нужно указать правильный телефон',
            ],

            [['msg_pers_email'], 'email', 'except' => ['importdata']],

            [['subject_id'], 'integer', ],
        ];
    }

    /**
     * Проверка на одно и тоже слово в полях ФИО
     *
     * @param $attribute
     * @param $params
     */
    public function filterUserName($attribute, $params) {
        Yii::info('filterUserName('.$attribute.'): ' . $this->msg_pers_name . $this->msg_pers_secname . $this->msg_pers_lastname);
        if( ($this->msg_pers_name == $this->msg_pers_secname)
            && ($this->msg_pers_name == $this->msg_pers_lastname) ) {
            $this->addError($attribute, 'Неправильное имя');
            Yii::info('filterUserName('.$attribute.'): error');
        }
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
            'msg_pers_name' => 'Имя',
            'msg_pers_secname' => 'Отчество',
            'msg_pers_lastname' => 'Фамилия',
            'msg_pers_email' => 'Email',
            'msg_pers_phone' => 'Телефон',
            'msg_pers_org' => 'Учреждение',

        ];
    }

    /**
     * Поля для проверки в разных сценариях
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios['newmsg'] = [
            'msg_pers_text',
            'is_satisfied',
        ];


        // Шаг 1: ввод персональной информации
        $scenarios['step_1'] = [
            'msg_pers_name',
            'msg_pers_secname',
            'msg_pers_lastname',
            'msg_pers_email',
            'msg_pers_phone',
        ];

        // Шаг 2: выбор темы
        $scenarios['step_2'] = array_merge(
            $scenarios['step_1'],
            [
                'subject_id',
                'is_satisfied',
            ]
        );

        // Шаг 3: ввод сообщения
        $scenarios['step_3'] = array_merge(
            $scenarios['step_2'],
            [
                'msg_pers_text',
            ]
        );

        return $scenarios;
    }

}
