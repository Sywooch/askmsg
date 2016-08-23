<?php

namespace app\models;

use app\components\ChangestateBehavior;
use Yii;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeBehavior;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\UploadedFile;

use app\models\User;
use app\models\File;
use app\models\Tags;
use app\models\Msgflags;
use app\models\Msganswers;
use app\models\Msgtags;
use app\models\Rolesimport;
use app\components\AttributewalkBehavior;
use app\components\NotificateBehavior;
use Httpful\Request;
use Httpful\Response;
use app\components\RustextValidator;
use app\models\Notificatelog;
use app\models\Orgsovet;
use app\models\Sovet;
use app\models\Mediateanswer;
use app\components\SwiftHeaders;

/**
 * This is the model class for table "{{%message}}".
 *
 * @property integer $msg_id
 * @property string $msg_createtime
 * @property integer $msg_active
 * @property string $msg_pers_name
 * @property string $msg_pers_secname
 * @property string $msg_pers_lastname
 * @property string $msg_pers_email
 * @property string $msg_pers_phone
 * @property string $msg_pers_org
 * @property integer $msg_pers_region
 * @property string $msg_pers_text
 * @property string $msg_comment
 * @property integer $msg_empl_id
 * @property string $msg_empl_command
 * @property string $msg_empl_remark
 * @property string $msg_answer
 * @property string $msg_answertime
 * @property string $msg_oldcomment
 * @property integer $msg_flag
 * @property integer $msg_subject
 * @property integer $ekis_id
 * @property integer $msg_curator_id
 * @property integer $msg_mark
 * @property integer $msg_mediate_answer_id
 * @property integer $msg_bitflag
 *
 *
 * @property string $employer
 * @property string $asker
 * @property string $askid
 * @property string $askcontacts
 * @property string $tags
 * @property string $testemail
 * @property string $marktext
 *
 */
class Message extends \yii\db\ActiveRecord
{
    const MAX_PERSON_TEXT_LENGTH = 4000;
    const KEY_STATMSG_DATA = 'count_message_flags';
    const USERTYPE_PERSON = 'user';
    const USERTYPE_ANSWER = 'answer';
    const USERTYPE_SOANSWER = 'soanswer';
    const USERTYPE_CURATOR = 'curator';

    const EXCAPTION_CODE_MSG_ON_SOGL = 1;
    const EXCAPTION_CODE_MSG_ON_MODARATE = 2;

    const FLAG_REASON_YES = 1;
    const FLAG_REASON_NO = 2;

    const BIT_REASON_YES = 1;
    const BIT_REASON_NO = 2;

    public $employer; // Ответчик
    public $asker; // Проситель
    public $askid; // Номер и дата
    public $askcontacts; // Email и телефон
    public $tags; //
    public $_tagsstring; // теги строкой
    public $testemail = ''; // проверочный email для оценки
    public $marktext = ''; // текст для оценки

    public $reasonable = null; // Флаг обосновано или нет обращение

    public $verifyCode;

    public $aMark = [
        5 => 'Да',
        0 => 'Нет',
    ];

    /**
     * @var mixed file аттрибут для генерации поля добавления файла
     */
    public $file;

    public $_oldAttributes = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     * @return array флаги, сообщения с которыми выводятся для пользователя с определенным уровнем доступа
     */
    public static function getMessageFilters()
    {
        // Флаги сообщений для разных пользователей
        $_flagFilter = [
            Rolesimport::ROLE_GUEST => [
                Msgflags::MFLG_THANK,
                Msgflags::MFLG_SHOW_REVIS,
                Msgflags::MFLG_SHOW_NO_ANSWER,
                Msgflags::MFLG_SHOW_ANSWER,
                Msgflags::MFLG_SHOW_INSTR,
                Msgflags::MFLG_SHOW_NEWANSWER,
                Msgflags::MFLG_SHOW_NOSOGL,
            ],
            Rolesimport::ROLE_MODERATE_DOGM => [
                Msgflags::MFLG_NEW,
//                Msgflags::MFLG_INT_FIN_INSTR,
//                Msgflags::MFLG_INT_NEWANSWER, // убрал 06.05.2015
//                Msgflags::MFLG_SHOW_NEWANSWER, // убрал 06.05.2015
            ],
            Rolesimport::ROLE_ANSWER_DOGM => [
//                Msgflags::MFLG_NEW,
                Msgflags::MFLG_INT_INSTR,
                Msgflags::MFLG_INT_REVIS_INSTR,
                Msgflags::MFLG_SHOW_INSTR,
                Msgflags::MFLG_SHOW_REVIS,
                Msgflags::MFLG_SHOW_NOSOGL,
                Msgflags::MFLG_INT_NOSOGL,
            ],
            Rolesimport::ROLE_ADMIN => [
                Msgflags::MFLG_THANK,
                Msgflags::MFLG_INT_FIN_INSTR,
                Msgflags::MFLG_INT_NEWANSWER,
                Msgflags::MFLG_INT_REVIS_INSTR,
                Msgflags::MFLG_INT_INSTR,
                Msgflags::MFLG_NOSHOW,
                Msgflags::MFLG_SHOW_REVIS,
                Msgflags::MFLG_SHOW_NO_ANSWER,
                Msgflags::MFLG_SHOW_ANSWER,
                Msgflags::MFLG_NEW,
                Msgflags::MFLG_SHOW_INSTR,
                Msgflags::MFLG_SHOW_NEWANSWER,
            ],
        ];
        return $_flagFilter;
    }

    /**
     * @inheritdoc
     */
    public function behaviors(){
        $a = [
            // сделаем первые буковки имени большими
            [
                'class' => AttributewalkBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname'],
                ],
                'value' => function ($event, $attribute) {
                    // тут еще, конечно, можно предусмотреть хитрые имена-фамилии типа Кара-Мурза, но пока не буду
                    /** @var  $model Activerecord */
                    $model = $event->sender;
                    $s = $model->$attribute;
                    $s = mb_strtoupper(mb_substr($s, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($s, 1, 255, 'UTF-8');
//                    Yii::info('Convert letter: ' . $model->$attribute . ' -> ' . $s);
                    return $s;
                },
            ],
        ];

        if( !in_array($this->scenario, ['importdata', 'setreason']) ) {
            $a = array_merge(
                $a,
                [
                    // поставим дату ответа
                    [
                        'class' => AttributewalkBehavior::className(),
                        'attributes' => [
//                            ActiveRecord::EVENT_BEFORE_VALIDATE => ['msg_answertime'],
                            ActiveRecord::EVENT_BEFORE_UPDATE => ['msg_answertime'],
                        ],
                        'value' => function ($event, $attribute) {
                            /** @var Message $model */
//                            if( $this->scenario != 'moderator' ) {
//                                return;
//                            }
                            $model = $event->sender;
                            $a = [Msgflags::MFLG_SHOW_ANSWER, Msgflags::MFLG_INT_FIN_INSTR];
                            if( in_array($model->msg_flag, $a)
                             && isset($model->_oldAttributes['msg_flag'])
                             && !in_array($model->_oldAttributes['msg_flag'], $a) ) {
                                $model->$attribute = new Expression('NOW()');
                            }
                        },
                    ],

                    // при добавлении сообщения
                    [
                        'class' =>  AttributewalkBehavior::className(),
                        'attributes' => [
                            ActiveRecord::EVENT_BEFORE_INSERT => ['msg_flag', 'msg_active'],
                        ],
                        'value' => function ($event, $attribute) {
                            $aVal = [
                                'msg_flag' => Msgflags::MFLG_NEW, // поставим флаг нового сообщения
                                'msg_active' => 1,                // поставим флаг активности сообщения
                            ];
                            if( isset($aVal[$attribute]) ) {
                                return $aVal[$attribute];
                            }
                            return null;
                        },
                    ],

                    // сохраним предыдущие аттрибуты
                    [
                        'class' => AttributeBehavior::className(),
                        'attributes' => [
                            ActiveRecord::EVENT_AFTER_FIND => '_oldAttributes',
                        ],
                        'value' => function ($event) {
                            /** @var Message $ob */
                            $ob = $event->sender;
                            return [
                                'msg_flag' => $ob->msg_flag,
                                'answers' => $ob->allanswers,
                                'msg_empl_id' => $ob->msg_empl_id,
                                'msg_curator_id' => $ob->msg_curator_id,
                            ];
                        },
                    ],

                    // пробуем посмотреть нужна ли отправка
                    [
                        'class' => AttributeBehavior::className(),
                        'attributes' => [
                            ActiveRecord::EVENT_AFTER_UPDATE => 'msg_flag',
                        ],
                        'value' => function ($event) {
                            /** @var Message $model */
//                            Yii::info('mail on EVENT_AFTER_UPDATE');
                            $model = $event->sender;
                            $model->sendUserNotification([
                                Message::USERTYPE_PERSON,
                                Message::USERTYPE_ANSWER,
                                Message::USERTYPE_SOANSWER,
                                Message::USERTYPE_CURATOR,
                            ]);
                            return $model->msg_flag;
                        },

                    ],

                ]
            );
        }

        return $a;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $fileCount = $this->countAvalableFile();
        $aFlagsToAnswer = [Msgflags::MFLG_INT_INSTR, Msgflags::MFLG_INT_REVIS_INSTR, Msgflags::MFLG_SHOW_INSTR,Msgflags::MFLG_SHOW_REVIS,];
        return [
            [['msg_pers_text'], 'filter', 'on'=>'person', 'filter' => function($val){ return strip_tags($val, '<p><br>');  }, ], // в пользовательском вводе удаляем теги
            [['msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', ], 'filter', 'on'=>'person', 'filter' => function($val){ return strip_tags($val);  }, ],

            [['msg_pers_name', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_pers_text', 'msg_pers_region', 'msg_mark', 'testemail', 'reasonable', ], 'required'],
            [['msg_answer'], 'required', 'on' => 'answer', ],

//            [['testemail'], 'email', ],
            [['testemail'], 'compare', 'compareValue' => $this->getTestCode(), ], // $this->msg_pers_email

            [['msg_pers_org', 'ekis_id', 'msg_subject', 'msg_pers_secname'], 'required', 'on'=>'person', ],
//            [['msg_pers_secname'], 'required', 'on'=>['answer', 'person', 'moderator']],
            [['msg_createtime', 'msg_answertime'], 'filter', 'filter' => function($v){ return empty($v) ? new Expression('NOW()') : $v; }],
            [['msg_createtime', 'msg_answertime'], 'safe'],
            [['msg_flag'], 'required'],
//            [['answers'], 'safe'],
            [['answers'], 'in', 'range' => array_keys(User::getGroupUsers(Rolesimport::ROLE_ANSWER_DOGM, ['us_active' => User::STATUS_ACTIVE], '{{val}}')), 'allowArray' => true],
            [['alltags'], 'in', 'range' => ($this->scenario != 'importdata') ? array_keys(ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_TAG, $this->msg_subject), 'tag_id', 'tag_title')) : [], 'allowArray' => true],
            [['file'], 'safe'],
            [['file'], 'file', 'maxFiles' => $fileCount, 'maxSize' => Yii::$app->params['message.file.maxsize'], 'extensions' => Yii::$app->params['message.file.ext']],
//            [['answers'], 'in', 'range' => array_keys(User::getGroupUsers(Rolesimport::ROLE_ANSWER_DOGM, '', '{{val}}')), 'allowArray' => true],
            [['ekis_id'], 'setupEkisData', 'on'=>['person', 'moderator'],],
            [['msg_id', 'msg_active', 'msg_pers_region', 'msg_empl_id', 'msg_flag', 'msg_subject', 'ekis_id', 'msg_curator_id', 'msg_mark'], 'integer'],
            [['msg_mark'], 'in', 'range' => array_keys($this->aMark), ],
            [['marktext'], 'required', 'when' => function($model){ return $model->msg_mark == 0; }, ],
            [['marktext'], 'string', 'min'=>24, ],
            [['marktext'], 'app\components\RustextValidator', 'capital' => 0.2, 'russian' => 0.8, ],

            [['msg_pers_text'], 'string', 'max' => self::MAX_PERSON_TEXT_LENGTH, 'min' => 32, 'on' => 'person', 'tooShort' => 'Напишите более подробное сообщение'],
            [['msg_pers_text'], 'app\components\RustextValidator', 'on' => 'person', 'capital' => 0.2, 'russian' => 0.8, ],

            [['msg_answer', 'msg_empl_command', 'msg_empl_remark', 'msg_comment', 'msg_pers_org'], 'string'],
            [['msg_answer'], 'filter', 'filter' => function($v){ return strip_tags($v, '<p><a><li><ol><ul><strong><b><em><i><u><h1><h2><h3><h4><h5><blockquote><pre><del><br>');  }],

            [['msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', ], 'filter', 'filter' => 'trim'],
            [['msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_oldcomment'], 'string', 'max' => 255],
            [['msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', ], 'match',
                'pattern' => '|^[А-Яа-яЁё]{2}[-А-Яа-яЁё\\s]*$|u', 'message' => 'Допустимы символы русского алфавита',
                'when' => function($model) { return ($this->scenario != 'importdata'); },
            ],
            [['msg_pers_name', ], 'filterUserName', 'on' => 'person', ],
            [['msg_pers_phone', ], 'match',
                'pattern' => '|^\\+7\\([\\d]{3}\\)\s+[\\d]{3}-[\\d]{2}-[\\d]{2}$|', 'message' => 'Нужно указать правильный телефон',
            ],

            ['verifyCode', 'captcha'],

            [['msg_pers_email'], 'email', 'except' => ['importdata']],
            [['msg_mediate_answer_id'], 'integer', ],
            [['employer', 'asker', 'askid', 'askcontacts', 'tags'], 'string', 'max' => 255],
            [['tagsstring'], 'string', 'max' => 1024],
            [['msg_empl_id', 'msg_empl_command'], 'required',
                'when' => function($model) use ($aFlagsToAnswer) { return ($this->scenario != 'importdata') && in_array($this->msg_flag, $aFlagsToAnswer); },
                'whenClient' => "function (attribute, value) { return [".implode(',', $aFlagsToAnswer)."].indexOf(parseInt($('#".Html::getInputId($this, 'msg_flag') ."').val())) != -1 ;}"
            ],
            [['msg_empl_remark'], 'required',
                'when' => function($model) { return in_array($this->msg_flag, [Msgflags::MFLG_INT_REVIS_INSTR, Msgflags::MFLG_SHOW_REVIS]); },
                'whenClient' => "function (attribute, value) { return [".implode(',', [Msgflags::MFLG_INT_REVIS_INSTR, Msgflags::MFLG_SHOW_REVIS])."].indexOf(parseInt($('#".Html::getInputId($this, 'msg_flag') ."').val())) != -1 ;}"
            ],

            ['reasonable', 'in', 'range' => array_keys($this->getAllReasons()), 'skipOnEmpty' => false, ],

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

    public function setupEkisData($attribute, $params) {
        $ob = $this->getEkisOrgData($this->ekis_id);
        if( $ob !== null ) {
//            $sOld = "{$this->msg_pers_org} + {$this->msg_pers_region}";
            $this->msg_pers_org = $ob['text'];
            $this->msg_pers_region = $ob['eo_district_name_id'];
            Regions::testExistRegion($ob['eo_district_name_id'], $ob['eo_district_name']);
//            Yii::info("setupEkisData({$this->ekis_id}): {$sOld} -> {$this->msg_pers_org} + {$this->msg_pers_region}");
/*
[eo_district_name] => Восточный
[eo_district_name_id] => 1
[id] => 12241
[text] => ГБОУ гимназия № 1404 "Гамма"
*/
        }
    }

    /**
     * Поля для проверки в разных сценариях
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['curatortest'] = [
            'msg_empl_remark',
            'msg_answer',
            'msg_flag',
        ];

        $scenarios['mark'] = [
            'msg_mark',
            'testemail',
            'marktext',
        ];

        $scenarios['person'] = [
            'msg_pers_name',
            'msg_pers_lastname',
            'msg_pers_email',
            'msg_pers_phone',
            'msg_pers_text',
            'msg_pers_secname',
            'msg_pers_org',
            'msg_pers_region',
            'msg_createtime',
            'msg_subject',
            'ekis_id',
            'file',
        ];

        $scenarios['moderator'] = array_merge(
            $scenarios['person'],
            [
                'msg_empl_command',
                'msg_empl_remark',
                'msg_comment',
                'msg_empl_id',
                'msg_flag',
                'msg_active',
                'answers',
                'msg_curator_id',
//                'alltags',
                'tagsstring',
                'msg_answertime',
            ],
            // добавляем модератору возможность подправить ответ
            in_array(
                $this->msg_flag,
                [
                    Msgflags::MFLG_SHOW_NEWANSWER,
                    Msgflags::MFLG_INT_NEWANSWER,
                    Msgflags::MFLG_SHOW_NOSOGL,
                    Msgflags::MFLG_INT_NOSOGL,
                ]
            ) ? ['msg_answer'] : []
        );

        $scenarios['importdata'] = [
            'msg_id',
            'msg_pers_name',
            'msg_pers_lastname',
            'msg_pers_email',
            'msg_pers_phone',
            'msg_pers_text',
            'msg_pers_secname',
            'msg_pers_org',
            'msg_pers_region',
            'msg_createtime',
            'msg_active',
            'msg_answer',
            'msg_oldcomment',
            'msg_flag',
            'msg_comment',
            'msg_empl_id',
            'msg_empl_command',
            'msg_empl_remark',
        ];

        $scenarios['setreason'] = ['reasonable'];

        if( $this->isUseCaptcha() ) {
            $scenarios['person'][] = 'verifyCode';
        }

        // у старых сообщений нет темы, ekis_id
        /*
        foreach(['msg_subject', 'ekis_id', 'msg_pers_org'] As $v) {
            $n = array_search($v, $scenarios['moderator'], true);
            if( $n !== false ) {
                unset($scenarios['moderator'][$n]);
            }
        }
        */

        $scenarios['answer'] = [
            'msg_answer',
            'msg_answertime',
            'msg_flag',
            'file',
        ];

        $scenarios['delete'] = [
            'msg_flag',
        ];

        return $scenarios;
    }

    public function getScenariosData()
    { // TODO: отправить это все в конфиг
        $a = [
            'person' => [
                'title' => 'Создать обращение',
                'form' => '_form'
            ],
            'moderator' => [
                'title' => 'Обработка обращения',
                'form' => '_form_v2'
            ],
            'answer' => [
                'title' => 'Написать ответ',
                'form' => '_formanswer'
            ],
        ];
        return isset($a[$this->scenario]) ? $a[$this->scenario] : $a['person'];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'msg_id' => 'Номер',
            'msg_createtime' => 'Дата',
            'msg_active' => 'Видимо',
            'msg_pers_name' => 'Имя',
            'msg_pers_secname' => 'Отчество',
            'msg_pers_lastname' => 'Фамилия',
            'msg_pers_email' => 'Email',
            'msg_pers_phone' => 'Телефон',
            'msg_pers_org' => 'Учреждение',
            'msg_pers_region' => 'Округ',
            'msg_pers_text' => 'Обращение',
            'msg_comment' => 'Комментарий',
            'msg_empl_id' => 'Исполнитель',
            'msg_empl_command' => 'Поручение исполнителю',
            'msg_empl_remark' => 'Замечание исполнителю',
            'msg_answer' => 'Ответ',
            'msg_answertime' => 'Дата ответа',
            'msg_oldcomment' => 'Старые теги',
            'msg_flag' => 'Состояние',
            'msg_subject' => 'Тема',
            'ekis_id' => 'Учреждение',
            'tagsstring' => 'Теги',
            'msg_curator_id' => 'Контролер',
            'verifyCode' => 'Код',
            'msg_mark' => 'Удовлетворены ли Вы качеством ответа?',
            'msg_bitflag' => 'Битовые флаги',

            'employer' => 'Исполнитель',
            'asker' => 'Проситель',
            'answers' => 'Соисполнители',
            'askid' => 'Номер и дата',
            'askcontacts' => 'Контакты',
            'tags' => 'Теги',
            'alltags' => 'Теги',
            'file' => 'Файл',
            'testemail' => 'Проверочный код',
            'marktext' => 'Причина',
            'reasonable' => 'Обоснованность обращения',
        ];
    }

    /*
     * Отношения к теме
     *
     */
    public function getSubject() {
        return $this->hasOne(Tags::className(), ['tag_id' => 'msg_subject']);
    }

    /*
     * Отношения к файлам
     *
     */
    public function getAttachments() {
        return $this->hasMany(
            File::className(),
            ['file_msg_id' => 'msg_id']
        );
    }

    /*
     * Отношения к Исполнителю
     *
     */
    public function getEmployee() {
        return $this->hasOne(User::className(), ['us_id' => 'msg_empl_id']);
    }

    /*
     * Отношения к Куратору
     *
     */
    public function getCurator() {
        return $this->hasOne(User::className(), ['us_id' => 'msg_curator_id']);
    }

    /*
     * Отношения к Региону
     *
     */
    public function getRegion() {
        return $this->hasOne(Regions::className(), ['reg_id' => 'msg_pers_region']);
    }

    /*
     * Отношения к флагу
     *
     */
    public function getFlag() {
        return $this->hasOne(Msgflags::className(), ['fl_id' => 'msg_flag']);
    }

    /**
     *  Связь с табличкой, соединяющей сообщения и его соисполнителей
     */
    public function getUsers() {
        return $this->hasMany(
            Msganswers::className(),
            ['ma_message_id' => 'msg_id']
        );
    }

    /**
     *  Связь сообщения и его соисполнителей
     */
    public function getAnswers() {
        return $this
            ->hasMany(
                User::className(),
                ['us_id' => 'ma_user_id'])
            ->via('users');
    }

    /**
     *  Установка соисполнителей
     * @param array $answers id soanswers for message
     */
    public function setAnswers($answers)
    {
        $this->answers = $answers;
    }

    /**
     *  Связь с табличкой, соединяющей сообщения и его теги
     */
    public function getMsgtags() {
        return $this->hasMany(
            Msgtags::className(),
            ['mt_msg_id' => 'msg_id']
        );
    }

    /**
     *  Связь сообщения и его тегов
     */
    public function getAlltags() {
        return $this
            ->hasMany(
                Tags::className(),
                ['tag_id' => 'mt_tag_id'])
            ->via('msgtags');
    }

    /**
     *  Установка тегов
     * @param array $tags id tags for validate
     */
    public function setAlltags($tags)
    {
        $this->alltags = $tags;
    }

    /**
     *  Связь с табличкой, хранящей лог оповещений
     */
    public function getNotifylog() {
        return $this->hasOne(
            Notificatelog::className(),
            ['ntflg_msg_id' => 'msg_id']
        );
    }

    /**
     *  Связь сообщения и его тегов строкой с разделителями Yii::$app->params['tag.separator']
     */
    public function getTagsstring() {
//        Yii::info(self::className() . '::getTagstring() this->alltags = ' . print_r(ArrayHelper::map($this->alltags, 'tag_id', 'tag_title'), true));
        $sSeparator = isset(Yii::$app->params['tag.separator']) ? Yii::$app->params['tag.separator'] : '|';
//        return implode(',', ArrayHelper::map($this->alltags, 'tag_id', 'tag_title'));
        return implode($sSeparator, ArrayHelper::map($this->alltags, 'tag_id', 'tag_title'));
    }

    /**
     *  Установка тегов по строке с разделителями Yii::$app->params['tag.separator']
     * @param string $val
     */
    public function setTagsstring($val) {
        $sSeparator = isset(Yii::$app->params['tag.separator']) ? Yii::$app->params['tag.separator'] : '|';
        Yii::info(self::className() . '::setTagstring() val = ' . $val);
        $a = explode($sSeparator, $val);
        foreach($a As $k => $v) {
            $v = trim($v);
            if( $v === '' ) {
                unset($a[$k]);
            }
        }
        Yii::info(self::className() . '::setTagstring() a = ' . print_r($a, true));
        $this->_tagsstring = $a;
    }

    /**
     *  Связь сообщения и номера совета директоров учреждения по ekis_id
     */
    public function getOrgsovet() {
        return $this->hasOne(Orgsovet::className(), ['orgsov_ekis_id' => 'ekis_id']);
    }

    /**
     *  Связь сообщения и совета директоров учреждения по ekis_id
     */
    public function getSovet() {
        return $this->hasOne(
                Sovet::className(),
                ['sovet_id' => 'orgsov_sovet_id']
            )
            ->via('orgsovet');
    }

    /**
     *  Связь сообщения и промежуточного ответа
     */
    public function getMediateanswer() {
        return $this->hasOne(
            Mediateanswer::className(),
            ['ma_msg_id' => 'msg_id']
        );
    }

    /**
     * Получение флага участия в рейтинге
     * @return mixed
     */
    public function getRaitngvalue() {
        return array_reduce(
            $this->alltags,
            function($carry, $el){
                /** @var Tags $el */
                return $carry || $el->tag_rating_val;
            },
            false
        );
    }

    /**
     *  Получение всех исполнителей
     * @return array
     */
    public function getAllanswers()
    {
        $a = [];
        if( $this->msg_empl_id != 0 ) {
            $a[] = $this->msg_empl_id;
        }

        if( $this->answers ) {
            foreach($this->answers As $ob) {
                if( is_object($ob) ) {
                    $a[] = $ob->us_id;
                }
                else {
                    $a[] = intval($ob);
                }
            }
        }
        return $a;
    }

    /**
     *  Полное имя просителя
     */
    public function getFullName() {
        return $this->msg_pers_lastname . ' ' . $this->msg_pers_name . ' ' . $this->msg_pers_secname;
    }

    /**
     *  Имя просителя без фамилии
     */
    public function getShortName() {
        $s = trim($this->msg_pers_name . ' ' . $this->msg_pers_secname);
        if( $s == '' ) {
            $s = $this->msg_pers_lastname;
        }
        return $s;
    }

    /**
     *  Существует ли промежуточный ответ
     */
    public function hasMediateanswer() {
        return $this->mediateanswer !== null;
    }

    /**
     *  Завершен ли промежуточный ответ
     */
    public function isMediateanswerFinished() {
        return $this->hasMediateanswer() && ($this->mediateanswer->ma_finished !== null);
    }

    /**
     *  Завершен ли промежуточный ответ
     */
    public function getFinalAnswer() {
        $s = '';

        if( $this->hasMediateanswer() ) {
            $s = $this->mediateanswer->ma_text;
        }

        if( ($this->isMediateanswerFinished() || !$this->hasMediateanswer()) && in_array($this->msg_flag, []) ) {
            $s = $this->msg_answer;
        }

        Yii::info('getFinalAnswer(): ['.$this->msg_id.'] hasMediateanswer() = ' . ($this->hasMediateanswer() ? 'true' : 'false') . ' isMediateanswerFinished() = ' . ($this->isMediateanswerFinished() ? 'true' : 'false') . ' s = ' . $s);
        return $s;
    }


    /**
     *  Возможность ответа
     */
    public function getIsAnswerble() {
        $bModerate = Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM);
        $aFlagAns = [
            Msgflags::MFLG_SHOW_INSTR,
            Msgflags::MFLG_INT_INSTR,
            Msgflags::MFLG_SHOW_REVIS,
            Msgflags::MFLG_INT_REVIS_INSTR,
        ];

/*
        if( $bModerate ) {
            $aFlagAns = array_merge(
                $aFlagAns,
                [
                    Msgflags::MFLG_SHOW_NEWANSWER,
                    Msgflags::MFLG_INT_NEWANSWER,
                ]
            );
        }
*/
        $bRet = ((Yii::$app->user->can(Rolesimport::ROLE_ANSWER_DOGM)
             && $this->msg_empl_id == Yii::$app->user->identity->getId())
             || $bModerate)
             && in_array($this->msg_flag, $aFlagAns)
             ;
        return $bRet;
    }

    /**
     *  Возможность проверки контролером
     */
    public function getIsControlable() {
        $bModerate = Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM);
        $aFlagAns = [
            Msgflags::MFLG_SHOW_NOSOGL,
            Msgflags::MFLG_INT_NOSOGL,
        ];

        $bRet = ((Yii::$app->user->can(Rolesimport::ROLE_ANSWER_DOGM)
             && $this->msg_empl_id == Yii::$app->user->identity->getId())
             || $bModerate)
             && in_array($this->msg_flag, $aFlagAns)
             ;
        return $bRet;
    }

    /**
     *  Сохраняем соисполнителей
     * @param Event $event
     */
    public function saveCoanswers($event) {
        $model = $event->sender;
        $model->saveRelateddata([
            'eventname' => $event->name,
            'reltableclass' => Msganswers::className(),
            'msgidfield' => 'ma_message_id',
            'relateidfield' => 'ma_user_id',
            'relateidarray' => $model->answers,
        ]);
    }

    /**
     *  Сохраняем теги
     * @param Event $event
     */
    public function saveAlltags($event) {
        $model = $event->sender;
        $aTagId = Tags::getIdByNames($this->_tagsstring);

        $model->saveRelateddata([
            'eventname' => $event->name,
            'reltableclass' => Msgtags::className(),
            'msgidfield' => 'mt_msg_id',
            'relateidfield' => 'mt_tag_id',
            'relateidarray' => $aTagId,
//            'relateidarray' => $model->alltags,
        ]);
    }

    /**
     *  Сохраняем соответствующие данные
     * @param array $param
     */
    public function saveRelateddata($param) {
        if( $param['eventname'] === ActiveRecord::EVENT_AFTER_UPDATE ) {
            $nCou = $param['reltableclass']::updateAll([$param['msgidfield'] => 0, $param['relateidfield'] => 0], $param['msgidfield'] . ' = ' . $this->msg_id);
        }
        if( is_array($param['relateidarray']) ) {
            foreach($param['relateidarray'] As $id) {
                $nUpd = Yii::$app
                    ->db
                    ->createCommand('Update ' . $param['reltableclass']::tableName() . ' Set '.$param['msgidfield'].' = :ma_message_id, '.$param['relateidfield'].' = :ma_user_id Where '.$param['msgidfield'].' = 0 Limit 1', [':ma_message_id' => $this->msg_id, ':ma_user_id' => $id])
                    ->execute();
                if( $nUpd == 0 ) {
                    Yii::$app
                        ->db
                        ->createCommand('Insert Into ' . $param['reltableclass']::tableName() . ' ('.$param['msgidfield'].', '.$param['relateidfield'].') Values (:ma_message_id,  :ma_user_id)', [':ma_message_id' => $this->msg_id, ':ma_user_id' => $id])
                        ->execute();
//                    Yii::info('Insert relate records : ['.$this->msg_id.', '.$id.']');
                }
            }
        }
    }

    /**
     * Process upload of file
     *
     */
    public function uploadFiles() {
        // TODO: вынести в отдельное поведение
        $files = UploadedFile::getInstances($this, 'file');
//        Yii::info("uploadFiles() files = " . count($files));

        // if no image was uploaded abort the upload
        if( empty($files) ) {
            return;
        }

        $nCou = $this->countAvalableFile();

        foreach($files As $ob) {
            /** @var  UploadedFile $ob */
            if( $nCou < 1 ) {
                break;
            }
            $oFile = new File();
            $oFile->addFile($ob, $this->msg_id, $this->scenario == 'person');
            if( $oFile->hasErrors() ) {
                Yii::info('uploadFiles(): File error: ' . print_r($oFile->getErrors(), true));
            }
            else {
                $nCou -= 1;
                Yii::info('uploadFiles(): save file ['.$nCou.'] ' . $oFile->file_orig_name . ' [' . $oFile->file_size . ']');
            }
        }
    }

    /**
     * Подсчет возможного количества загружаемых файлов
     *
     * @return int
     */
    public function countAvalableFile() {
        $n = Yii::$app->params['message.file.newcount'];
        if( !$this->isNewRecord ) {
            $n = Yii::$app->params['message.file.answercount'];
            foreach($this->attachments As $ob) {
                /** @var File  $ob */
                if( $ob->file_user_id !== null ) {
                    $n -= 1;
                }
//                Yii::info("countAvalableFile() [{$n}]" . print_r($ob->attributes, true));
            }
            if( $n < 0 ) {
                $n = 0;
            }
        }
//        Yii::info("countAvalableFile() return {$n}");
        return $n;
    }

    /**
     * Get user Files
     *
     * @param $bGuest boolean true - guest file, false - answer file
     * @return array of File objects
     */
    public function getUserFiles($bGuest = true) {
        $aRet = [];
        foreach($this->attachments As $ob) {
            /** @var File  $ob */
            if( $bGuest ) {
                if( $ob->file_user_id == 0 ) {
                    $aRet[] = $ob;
                }
            }
            else {
                if( $ob->file_user_id != 0 ) {
                    $aRet[] = $ob;
                }
            }
        }
        return $aRet;
    }

    /**
     * Проверка на смену флага
     * @return boolean
     *
     */
    public function isFlagChanged() {
        if( !isset($this->_oldAttributes['msg_flag']) ) {
            $this->_oldAttributes['msg_flag'] = 0;
        }

        $bRet = ($this->_oldAttributes['msg_flag'] != $this->msg_flag);
        return $bRet;
    }

    /**
     *
     * @param string $sType тип данных для возврата: флаги для отправки уведомлений по пользователям, исполнителям и соисполнителям
     * @return array массив переходов флагов: ключ - текущий флаг, значение - массив флагов, при переходе из которых будет присходить отправка писем
     *
     */
    public function getTransflags($sType = '')
    {
        $transTable = [
            self::USERTYPE_PERSON => [
                Msgflags::MFLG_SHOW_NO_ANSWER => [Msgflags::MFLG_NEW],
                Msgflags::MFLG_SHOW_INSTR => [Msgflags::MFLG_NEW],
                Msgflags::MFLG_SHOW_ANSWER => [Msgflags::MFLG_SHOW_INSTR, Msgflags::MFLG_SHOW_NEWANSWER, Msgflags::MFLG_SHOW_REVIS, Msgflags::MFLG_SHOW_NOSOGL, ],
                Msgflags::MFLG_INT_FIN_INSTR => [],
                Msgflags::MFLG_INT_INSTR => [Msgflags::MFLG_NEW],
            ],
            self::USERTYPE_ANSWER => [
                Msgflags::MFLG_SHOW_INSTR => [],
                Msgflags::MFLG_SHOW_REVIS => [],
                Msgflags::MFLG_INT_INSTR => [],
                Msgflags::MFLG_INT_REVIS_INSTR => [],
                Msgflags::MFLG_INT_NEWANSWER => [],
                Msgflags::MFLG_SHOW_NEWANSWER => [],
                Msgflags::MFLG_NEW => [
                    Msgflags::MFLG_SHOW_INSTR,
                    Msgflags::MFLG_SHOW_REVIS,
                    Msgflags::MFLG_INT_INSTR,
                    Msgflags::MFLG_INT_REVIS_INSTR,
                    Msgflags::MFLG_INT_NEWANSWER,
                    Msgflags::MFLG_SHOW_NEWANSWER,
                ],
            ],
            self::USERTYPE_CURATOR => [
                Msgflags::MFLG_SHOW_INSTR => [],
                Msgflags::MFLG_SHOW_REVIS => [],
                Msgflags::MFLG_INT_INSTR => [],
                Msgflags::MFLG_INT_REVIS_INSTR => [],
//                Msgflags::MFLG_INT_NEWANSWER => [],
//                Msgflags::MFLG_SHOW_NEWANSWER => [],
                Msgflags::MFLG_SHOW_NOSOGL => [],
                Msgflags::MFLG_INT_NOSOGL => [],
            ],
            self::USERTYPE_SOANSWER => [
                Msgflags::MFLG_SHOW_INSTR => [],
                Msgflags::MFLG_INT_INSTR => [],
            ],
        ];

        return $transTable[$sType];
    }

    /**
     *
     * @param string $sType тип данных для возврата: флаги для отправки уведомлений по пользователям, исполнителям и соисполнителям
     * @return boolean надо ли отправлять уведомление
     *
     */
    public function isNeedNotificate($sType = '') {
        $bRet = $this->isFlagChanged();
//        Yii::info('isNeedNotificate('.$sType.') flag ' . ($bRet ? '' : 'not ') . 'changed');

        if( $bRet ) {
            $transTable = $this->getTransflags($sType);
            if( !isset($transTable[$this->msg_flag])
             || ( !empty($transTable[$this->msg_flag]) && !in_array($this->_oldAttributes['msg_flag'], $transTable[$this->msg_flag]))
            ) {
                $bRet = false;
//                Yii::info('isNeedNotificate('.$sType.') = false [' . implode(',', (isset($transTable[$this->msg_flag]) && is_array($transTable[$this->msg_flag])) ? $transTable[$this->msg_flag] : []) . '] ' . $this->_oldAttributes['msg_flag'] . ' -> ' . $this->msg_flag);
            }
            else {
//                Yii::info('isNeedNotificate('.$sType.') = true [' . implode(',', $transTable[$this->msg_flag]) . '] ' . $this->_oldAttributes['msg_flag'] . ' -> ' . $this->msg_flag);
            }
        }
        return $bRet;
    }

    /**
     * Отправка уведомлений пользователю
     * @param string|array $aType тип сообщений для отправки
     *
     */
    public function sendUserNotification($aType = '') {
        $aMessages = [];
        if( is_string($aType) ) {
            $aType = [$aType];
        }

        $aTemplates = [
            self::USERTYPE_PERSON => [
                Msgflags::MFLG_SHOW_NO_ANSWER => 'user_notif_show',
                Msgflags::MFLG_SHOW_INSTR => 'user_notif_show',
                Msgflags::MFLG_INT_INSTR => 'user_notif_int',
                Msgflags::MFLG_SHOW_ANSWER => 'user_notif_answer',
                Msgflags::MFLG_INT_FIN_INSTR => 'user_notif_intanswer',
            ],
            self::USERTYPE_ANSWER => [
                Msgflags::MFLG_SHOW_INSTR => 'ans_notif_instr',
                Msgflags::MFLG_INT_INSTR => 'ans_notif_instr',
                Msgflags::MFLG_SHOW_REVIS => 'ans_notif_revis',
                Msgflags::MFLG_INT_REVIS_INSTR => 'ans_notif_revis',
                Msgflags::MFLG_NEW => 'ans_notif_esc',
                Msgflags::MFLG_INT_NEWANSWER => 'ans_notif_answer',
                Msgflags::MFLG_SHOW_NEWANSWER => 'ans_notif_answer',
            ],
            self::USERTYPE_CURATOR => [
                Msgflags::MFLG_SHOW_INSTR => 'curator_notif_instr',
                Msgflags::MFLG_INT_INSTR => 'curator_notif_instr',
                Msgflags::MFLG_SHOW_REVIS => 'curator_notif_revis',
                Msgflags::MFLG_INT_REVIS_INSTR => 'curator_notif_revis',
//                Msgflags::MFLG_INT_NEWANSWER => 'curator_notif_answer',
//                Msgflags::MFLG_SHOW_NEWANSWER => 'curator_notif_answer',
                Msgflags::MFLG_SHOW_NOSOGL => 'curator_notif_answer',
                Msgflags::MFLG_INT_NOSOGL => 'curator_notif_answer',
            ],
            self::USERTYPE_SOANSWER => [
                Msgflags::MFLG_SHOW_INSTR => 'soans_notif_instr',
                Msgflags::MFLG_INT_INSTR => 'soans_notif_instr',
            ]
        ];

        foreach($aType As $sType) {
            $aMessages = array_merge($aMessages, $this->getChangePersonNotifyMail($sType));

            if ($this->isNeedNotificate($sType)) {
                Yii::info('sendUserNotification(' . $sType . ') need notify');
                if (!isset($aTemplates[$sType])) {
                    Yii::info('sendUserNotification(' . $sType . ') ERROR: not found notification template');
                    continue;
                }

                $sTemplate = $aTemplates[$sType][$this->msg_flag];

                switch ($sType) {
                    case self::USERTYPE_PERSON:
                        Yii::info('sendUserNotification(' . $sType . ') [' . $sTemplate . '] ' . $this->msg_id . ' -> ' . $this->msg_pers_email);
                        $aMessages[] = Yii::$app->mailer->compose($sTemplate, ['model' => $this,])
                            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                            ->setTo($this->msg_pers_email)
                            ->setSubject('Обращение №' . $this->msg_id . ' от ' . date('d.m.Y', strtotime($this->msg_createtime)));
                        break;

                    case self::USERTYPE_ANSWER:
                        Yii::info('sendUserNotification(' . $sType . ') [' . $sTemplate . '] ' . $this->msg_id . ' -> ' . $this->employee->us_email);
                        $aMessages[] = Yii::$app->mailer->compose($sTemplate, ['model' => $this,])
                            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                            ->setTo($this->employee->us_email)
                            ->setSubject('Обращение №' . $this->msg_id . ' от ' . date('d.m.Y', strtotime($this->msg_createtime)));
                        break;

                    case self::USERTYPE_SOANSWER:
                        $aFiles = $this->getUserFiles(true);
                        $a = User::find()->where(['us_id' => array_slice($this->getAllanswers(), 1)])->all();
                        foreach ($a As $ob) {
                            Yii::info('sendUserNotification(' . $sType . ') [' . $sTemplate . '] ' . $this->msg_id . ' -> ' . $ob->us_email);
                            $oMsg = Yii::$app->mailer->compose($sTemplate, ['model' => $this, 'user' => $ob, 'allusers' => $a, 'mainuser' => $this->employee])
                                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                                ->setTo($ob->us_email)
                                ->setSubject('Обращение №' . $this->msg_id . ' от ' . date('d.m.Y', strtotime($this->msg_createtime)));
                            if (count($aFiles) > 0) {
                                foreach ($aFiles As $obFile) {
                                    /** @var File $obFile */
                                    $oMsg->attach($obFile->getFullpath(), ['fileName' => $obFile->file_orig_name]);
                                }
                            }
                            $aMessages[] = $oMsg;
                        }
                        break;

                    case self::USERTYPE_CURATOR:
//                        Yii::info('sendUserNotification(' . $sType . ') case self::USERTYPE_CURATOR');
                        if( $this->curator !== null ) {
                            $aFiles = array_merge($this->getUserFiles(true), $this->getUserFiles(false));
                            $a = User::find()->where(['us_id' => array_slice($this->getAllanswers(), 1)])->all();
                            $oMsg = Yii::$app->mailer->compose($sTemplate, ['model' => $this, 'allusers' => $a,])
                                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                                ->setTo($this->curator->us_email)
                                ->setSubject('Обращение №' . $this->msg_id . ' от ' . date('d.m.Y', strtotime($this->msg_createtime)));

                            if (count($aFiles) > 0) {
                                foreach ($aFiles As $obFile) {
                                    /** @var File $obFile */
                                    $oMsg->attach($obFile->getFullpath(), ['fileName' => $obFile->file_orig_name]);
                                }
                            }
                            $aMessages[] = $oMsg;
                        }
//                        else {
//                            Yii::info('sendUserNotification(' . $sType . ') curator === null');
//                        }
                        break;
                } // switch ($sType)
            } // if ($this->isNeedNotificate($sType))
            else {
                Yii::info('sendUserNotification(' . $sType . ') NOT need notify');
            }
        } // foreach($aType As $sType)

        if( count($aMessages) > 0 ) {
            foreach($aMessages As $k=>$v) {
                SwiftHeaders::setAntiSpamHeaders($aMessages[$k], ['email' => Yii::$app->params['supportEmail']]);
            }
            Yii::$app->mailer->sendMultiple($aMessages);
        }
    }

    /**
     * Добавление шаблонов для отправки писем при смене ответственных лиц
     * @param string $type
     * @return array
     */
    public function getChangePersonNotifyMail($type) {

        Yii::info('getChangePersonNotifyMail('.$type.')');
        $aRet = [];
        $a = null;
        switch($type) {
            case self::USERTYPE_ANSWER:
                Yii::info('getChangePersonNotifyMail('.$type.'): USERTYPE_ANSWER ' . $this->_oldAttributes['msg_empl_id'] . ' -> ' . $this->msg_empl_id);
                if( !empty($this->_oldAttributes['msg_empl_id']) && ($this->_oldAttributes['msg_empl_id'] != $this->msg_empl_id) ) {
                    // сменился ответчик
                    // Отправить старому письмо, что он снят и новому, что назначен
                    Yii::info('getChangePersonNotifyMail('.$type.'): '.$this->_oldAttributes['msg_empl_id'].' != '.$this->msg_empl_id);
                    $aUsers = User::findAll(['us_id' => [$this->_oldAttributes['msg_empl_id'], intval($this->msg_empl_id, 10)]]);
                    unset($this->employee);
                    foreach($aUsers As $ob) {
                        if( $ob->us_id == $this->msg_empl_id ) {
                            $sTemplate = 'ans_notif_instr';
                            $email = $ob->us_email;
                        }
                        else {
                            $sTemplate = 'ans_notif_noneed';
                            $email = $ob->us_email;
                        }
                        Yii::info('getChangePersonNotifyMail('.$type.'): us_id = '.$ob->us_id.' -> ' . $sTemplate . ", " . $email);
                        $aRet[] = Yii::$app->mailer->compose($sTemplate, ['model' => $this, 'user' => $ob])
                            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                            ->setTo($email)
                            ->setSubject('Обращение №' . $this->msg_id . ' от ' . date('d.m.Y', strtotime($this->msg_createtime)));
                    }
                }
                break;
            case self::USERTYPE_CURATOR:
                Yii::info('getChangePersonNotifyMail('.$type.'): USERTYPE_CURATOR ' . $this->_oldAttributes['msg_curator_id'] . ' -> ' . $this->msg_curator_id);
                if( !empty($this->_oldAttributes['msg_curator_id']) && ($this->_oldAttributes['msg_curator_id'] != $this->msg_curator_id) ) {
                    // сменился куратор
                    // Отправить старому письмо, что он снят и новому, что назначен
                    Yii::info('getChangePersonNotifyMail('.$type.'): '.$this->_oldAttributes['msg_curator_id'].' != '.$this->msg_curator_id);
                    $aUsers = User::findAll(['us_id' => [$this->_oldAttributes['msg_curator_id'], intval($this->msg_curator_id, 10)]]);
                    $a = User::find()->where(['us_id' => array_slice($this->getAllanswers(), 1)])->all();
                    $aFiles = array_merge($this->getUserFiles(true), $this->getUserFiles(false));
                    unset($this->curator);
                    foreach($aUsers As $ob) {
                        if( $ob->us_id == $this->msg_curator_id ) {
                            $sTemplate = 'curator_notif_instr';
                            $email = $ob->us_email;
                        }
                        else {
                            $sTemplate = 'curator_notif_noneed';
                            $email = $ob->us_email;
                        }
                        Yii::info('getChangePersonNotifyMail('.$type.'): us_id = '.$ob->us_id.' -> ' . $sTemplate . ", " . $email);
                        $oMsg = Yii::$app->mailer->compose($sTemplate, ['model' => $this, 'allusers' => $a, 'user' => $ob,])
                            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                            ->setTo($email)
                            ->setSubject('Обращение №' . $this->msg_id . ' от ' . date('d.m.Y', strtotime($this->msg_createtime)));
                        if (count($aFiles) > 0) {
                            foreach ($aFiles As $obFile) {
                                /** @var File $obFile */
                                $oMsg->attach($obFile->getFullpath(), ['fileName' => $obFile->file_orig_name]);
                            }
                        }
                        $aRet[] = $oMsg;
                    }
                }
                break;
            case self::USERTYPE_SOANSWER:
                break;
        }
        return $aRet;
    }


    /**
     * @param $id
     * @return mixed|null
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getEkisOrgData($id) {
        $ob = null;
        if( $id > 0 ) {
            $data = [
                'filters' => [
                    'eo_id' => $id,
                ],
                'maskarade' => [
                    'eo_id' => "id",
                    'eo_short_name' => "text",
                ],
                'fields' => implode(";", ["eo_id", "eo_short_name", "eo_district_name_id", "eo_district_name"]),
            ];
            $request = Request::post('http://hastur.temocenter.ru/task/eo.search/') // , http_build_query($data), 'application/x-www-form-urlencoded'
                ->addHeader('Accept', 'application/json; charset=UTF-8')
                ->body(http_build_query($data))
                ->contentType('application/x-www-form-urlencoded');

            /** @var Response $response */
            $response = $request->send();
            $aData = json_decode($response->body, true);
            if( isset($aData['total']) && ($aData['total'] > 0) ) {
                $ob = array_pop($aData['list']);
            }
        }
        return $ob;

    }

    /**
     * Нужно ли использовать капчу
     *
     * @return bool
     */
    public function isUseCaptcha()
    {
        return isset(Yii::$app->params['msgform.use.captcha'])
            && Yii::$app->params['msgform.use.captcha'];
    }

    /**
     * Кодируем данные
     *
     * @return string
     */
    public static function encodeData($key, $method, $iv, $data)
    {
//        $sDate = preg_replace('|[^\\d]|', '', $this->msg_createtime);
        return bin2hex(openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv));
    }

    /**
     * Декодируем данные
     *
     * @return string
     */
    public static function decodeData($key, $method, $iv, $data)
    {
        return openssl_decrypt(hex2bin($data), $method, $key, OPENSSL_RAW_DATA, $iv);
    }

    /**
     * Кодируем адрес
     *
     * @return string
     */
    public function getMarkUrl()
    {
        $method = Yii::$app->params['message.encode.method'];
        $key = Yii::$app->params['message.encode.key'];
        $iv = Yii::$app->params['message.encode.iv'];
        return ['mark', 'sign' => self::encodeData($key, $method, $iv, $this->msg_id)];
    }

    /**
     * Получаем проверочный код
     *
     * @return string
     */
    public function getTestCode()
    {
//        $method = Yii::$app->params['message.encode.method'];
//        $key = Yii::$app->params['message.encode.key'];
//        $iv = Yii::$app->params['message.encode.iv'];
//        return substr(self::encodeData($key, $method, $iv, $this->msg_createtime), -4);
        $sDate = preg_replace('|[^\\d]|', '', $this->msg_createtime);
        return substr($sDate, -4);
    }

    /**
     * Декодируем адрес
     *
     * @return string
     */
    public static function findModelFromMarkUrl()
    {
        $method = Yii::$app->params['message.encode.method'];
        $key = Yii::$app->params['message.encode.key'];
        $iv = Yii::$app->params['message.encode.iv'];
        $sign = Yii::$app->request->getQueryParam('sign', '');
        $id = self::decodeData($key, $method, $iv, $sign);
        $model = Message::findOne($id);
        return $model;
    }

    /**
     *
     * Проба на определение пола по имени
     *
     * @return string
     */
    public function tryGender() {
        $sEnc = 'UTF-8';
        $sname = mb_strtolower($this->msg_pers_name, $sEnc);
        $ssecname = mb_strtolower($this->msg_pers_secname, $sEnc);
        $slastname = mb_strtolower($this->msg_pers_lastname, $sEnc);
        $slast = mb_substr($sname, -1, 1, $sEnc);
        $g = '';
        if( ($slast == 'а') || ($slast == 'я') ) {
            $g = 'ж';
            if( in_array($sname, ['ваня', 'петя', 'саня', 'сеня', 'илья', 'гаврила', ]) ) {
                $g = 'м';
            }
        }
        else if( $slast == 'и' ) {
            $g = 'м';
            if( (mb_substr($ssecname, -1, 1, $sEnc) == 'а') || (mb_substr($slastname, -1, 1, $sEnc) == 'а') ) {
                $g = 'ж';
            }
        }
        else if( $sname == 'любовь' ) {
            $g = 'ж';
        }
        else {
            $g = 'м';
            if( (mb_substr($ssecname, -1, 1, $sEnc) == 'а') || (mb_substr($slastname, -1, 1, $sEnc) == 'а') ) {
                $g = 'ж';
            }
            else if( in_array($sname, ['айсель', 'мелине', 'набат', 'наринэ', 'армине', 'гузель', 'биргюль', 'марине', ]) ) {
                $g = 'ж';
            }
        }

        return $g;
    }


    /**
     *
     * Нужно ли показывать неавторизированным пользователям
     *
     * @return bool
     */
    public function isHidden() {
        $aHiddenMessageFlags = [
            Msgflags::MFLG_NEW,
            Msgflags::MFLG_INT_NOSOGL,
            Msgflags::MFLG_INT_NEWANSWER,
            Msgflags::MFLG_INT_FIN_INSTR,
            Msgflags::MFLG_INT_INSTR,
            Msgflags::MFLG_INT_REVIS_INSTR,
            Msgflags::MFLG_NOSHOW,
        ];

        return in_array($this->msg_flag, $aHiddenMessageFlags);
    }

    /**
     *
     * Запаковать флаги в битовое поле сообщения
     *
     */
    public function zipFlags() {
        // биты флага Обосновано или нет
        $this->msg_bitflag = ($this->reasonable === null ? 0 : ($this->reasonable == self::FLAG_REASON_YES ? self::BIT_REASON_YES : self::BIT_REASON_NO));
    }

    /**
     *
     * Распаковать битовое поле сообщения в флаги модели
     *
     */
    public function unzipFlags() {
        // Обосновано или нет
        $this->reasonable = ($this->msg_bitflag & self::BIT_REASON_YES) ? self::FLAG_REASON_YES : (($this->msg_bitflag & self::BIT_REASON_NO) ? self::FLAG_REASON_NO : null);
    }

    /**
     *
     * Проверка - нужно ли устанвлисить флаг обоснованности обращения
     *
     * @return bool
     */
    public function isNeedSetReasonble() {
        $a = [Msgflags::MFLG_SHOW_ANSWER, Msgflags::MFLG_INT_FIN_INSTR];
        return in_array($this->msg_flag, $a)
            && isset($this->_oldAttributes['msg_flag'])
            && !in_array($this->_oldAttributes['msg_flag'], $a);
    }

    /**
     *
     * Получение списка всех варантов для обоснованности олбращения
     *
     * @return array
     */
    public function getAllReasons() {
        return [
            self::FLAG_REASON_YES => 'Обращение обосновано',
            self::FLAG_REASON_NO => 'Обращение необосновано',
        ];
    }

    /**
     *
     * Получение текста обоснованности
     *
     * @return string
     *
     */
    public function getReasonText() {
        $a = $this->getAllReasons();
        return isset($a[$this->reasonable]) ? $a[$this->reasonable] : '';
    }

    /**
     *
     * Установка флагов сообщения при установке обоснованности
     * @param boolean $bSetFinish устанавливать окончательные флаги - true, устанавливать предыдущие флаги - false
     *
     */
    public function setMessageFlagForReason($bSetFinish = true) {
        $a = [
            Msgflags::MFLG_SHOW_ANSWER => Msgflags::MFLG_SHOW_NEWANSWER,
            Msgflags::MFLG_INT_FIN_INSTR => Msgflags::MFLG_INT_NEWANSWER,
        ];

        if( $bSetFinish ) {
            $a = array_flip($a);
        }

        if( isset($a[$this->msg_flag]) ) {
            Yii::info('Set flag [' . $this->msg_id . ']: ' . $this->msg_flag . ' -> ' . $a[$this->msg_flag]);
            $this->msg_flag = $a[$this->msg_flag];
        }
    }

}
