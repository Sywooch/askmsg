<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\SubjectTree;

/**
 * ContactForm is the model behind the contact form.
 */
class MessageTreeForm extends Model
{
    const MAX_PERSON_TEXT_LENGTH = 4000;

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
    public $is_ask_director;
    public $ekis_id;
    public $file;
    public $is_user_variant;
    public $msg_pers_subject;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['is_satisfied', 'is_ask_director', 'ekis_id', 'is_user_variant', ], 'integer', ],
            [['is_satisfied', 'is_ask_director', ], 'in', 'range' => [1, 2], ],

            [['msg_pers_text', 'msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'subject_id', ], 'required'],
            [['msg_pers_text'], 'string', 'min' => 100, 'max' => self::MAX_PERSON_TEXT_LENGTH, ],

            [['msg_pers_subject', ], 'required', 'when' => function($model) { Yii::info('validator when: ' . print_r($model->attributes, true)); return (intval($model->is_user_variant) > 0); },],

            [['file'], 'safe'],
            [['file'], 'file', 'maxFiles' => 1, 'maxSize' => Yii::$app->params['message.file.maxsize'], 'extensions' => Yii::$app->params['message.file.ext']],

            [['msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', ], 'filter', 'filter' => 'trim'],
            [['msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_pers_region', 'msg_pers_org', 'msg_pers_subject', ], 'string', 'max' => 255],
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
            'ekis_id' => 'Учреждение',
            'is_ask_director' => 'Обращались ли к директору',
            'file' => 'Файл',
            'is_user_variant' => 'Свой вариант',
            'msg_pers_subject' => 'Тема',
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
            'ekis_id',
            'msg_pers_org',
            'msg_pers_region',
        ];

        // Шаг 2: выбор темы
        $scenarios['step_2'] = array_merge(
            $scenarios['step_1'],
            [
                'subject_id',
                'is_satisfied',
                'is_ask_director',
                'is_user_variant',
            ]
        );

        // Шаг 3: ввод сообщения
        $scenarios['step_3'] = array_merge(
            $scenarios['step_2'],
            [
                'msg_pers_subject',
                'msg_pers_text',
            ]
        );

        return $scenarios;
    }

    /**
     * @param SubjectTree $model
     * @return bool
     */
    public function isNeedSatisfy($model) {
        $bRet = (count($model->getChild()) == 0)
            && !empty($model->subj_info)
            && (intval($this->is_satisfied) == 0);

        Yii::info(
            'isNeedSatisfy['.$model->subj_id.'] child: ' . count($model->getChild()) . "\n"
            . ' subj_info = ' . $model->subj_info . "\n"
            . ' is_satisfied = ' . intval($this->is_satisfied) . "\n"
            . ' = ' . ($bRet ? 'true' : 'false')
        );
        return $bRet;
    }

    /**
     * @param SubjectTree $model
     * @return bool
     */
    public function isNeedAskdirector($model) {
        $bRet = (count($model->getChild()) == 0)
            && !empty($model->subj_final_question)
            && (intval($this->is_ask_director) == 0);
        Yii::info(
            'isNeedAskdirector['.$model->subj_id.'] child: ' . count($model->getChild()) . "\n"
            . ' subj_final_question = ' . $model->subj_final_question . "\n"
            . ' is_ask_director = ' . intval($this->is_ask_director) . "\n"
            . ' = ' . ($bRet ? 'true' : 'false')
        );

        return $bRet;
    }

    /**
     * @param SubjectTree $model
     * @return bool
     */
    public function isNeedSelectChild($model) {
        return (count($model->getChild()) > 0);
    }

}
