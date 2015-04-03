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
 *
 *
 * @property string $employer
 * @property string $asker
 * @property string $askid
 * @property string $askcontacts
 * @property string $tags
 *
 */
class Message extends \yii\db\ActiveRecord
{
    const MAX_PERSON_TEXT_LENGTH = 4000;
    const KEY_STATMSG_DATA = 'count_message_flags';
    const USERTYPE_PERSON = 'user';
    const USERTYPE_ANSWER = 'answer';
    const USERTYPE_SOANSWER = 'soanswer';

    public $employer; // Ответчик
    public $asker; // Проситель
    public $askid; // Номер и дата
    public $askcontacts; // Email и телефон
    public $tags; //
    public $_tagsstring; // теги строкой
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
            ],
            Rolesimport::ROLE_MODERATE_DOGM => [
                Msgflags::MFLG_NEW,
//                Msgflags::MFLG_INT_FIN_INSTR,
                Msgflags::MFLG_INT_NEWANSWER,
                Msgflags::MFLG_SHOW_NEWANSWER,
            ],
            Rolesimport::ROLE_ANSWER_DOGM => [
//                Msgflags::MFLG_NEW,
                Msgflags::MFLG_INT_INSTR,
                Msgflags::MFLG_INT_REVIS_INSTR,
                Msgflags::MFLG_SHOW_INSTR,
                Msgflags::MFLG_SHOW_REVIS,
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

        if( $this->scenario != 'importdata' ) {
            $a = array_merge(
                $a,
                [
                    // поставим дату ответа
                    [
                        'class' => AttributewalkBehavior::className(),
                        'attributes' => [
                            ActiveRecord::EVENT_BEFORE_VALIDATE => ['msg_answertime'],
                        ],
                        'value' => function ($event, $attribute) {
                            /** @var Message $model */
                            if( $this->scenario != 'moderator' ) {
                                return;
                            }
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
                            Yii::info('mail on EVENT_AFTER_UPDATE');
                            $model = $event->sender;
                            $model->sendUserNotification([
                                Message::USERTYPE_PERSON,
                                Message::USERTYPE_ANSWER,
                                Message::USERTYPE_SOANSWER,
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
            [['msg_pers_name', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_pers_text', 'msg_pers_region'], 'required'],
            [['msg_answer'], 'required', 'on' => 'answer', ],
            [['msg_pers_org', 'ekis_id', 'msg_subject', 'msg_pers_secname'], 'required', 'on'=>'person', ],
//            [['msg_pers_secname'], 'required', 'on'=>['answer', 'person', 'moderator']],
            [['msg_createtime', 'msg_answertime'], 'filter', 'filter' => function($v){ return empty($v) ? new Expression('NOW()') : $v; }],
            [['msg_createtime', 'msg_answertime'], 'safe'],
            [['msg_flag'], 'required'],
//            [['answers'], 'safe'],
            [['answers'], 'in', 'range' => array_keys(User::getGroupUsers(Rolesimport::ROLE_ANSWER_DOGM, '', '{{val}}')), 'allowArray' => true],
            [['alltags'], 'in', 'range' => ($this->scenario != 'importdata') ? array_keys(ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_TAG), 'tag_id', 'tag_title')) : [], 'allowArray' => true],
            [['file'], 'safe'],
            [['file'], 'file', 'maxFiles' => $fileCount, 'maxSize' => Yii::$app->params['message.file.maxsize'], 'extensions' => Yii::$app->params['message.file.ext']],
//            [['answers'], 'in', 'range' => array_keys(User::getGroupUsers(Rolesimport::ROLE_ANSWER_DOGM, '', '{{val}}')), 'allowArray' => true],
            [['ekis_id'], 'setupEkisData', 'on'=>'person',],
            [['msg_id', 'msg_active', 'msg_pers_region', 'msg_empl_id', 'msg_flag', 'msg_subject', 'ekis_id'], 'integer'],
            [['msg_pers_text'], 'string', 'max' => self::MAX_PERSON_TEXT_LENGTH, 'on' => 'person'],
            [['msg_answer', 'msg_empl_command', 'msg_empl_remark', 'msg_comment', 'msg_pers_org'], 'string'],
            [['msg_answer'], 'filter', 'filter' => function($v){ return strip_tags($v, '<p><a><li><ol><ul><strong><b><em><i><u><h1><h2><h3><h4><h5><blockquote><pre><del><br>');  }],
            [['msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_oldcomment'], 'string', 'max' => 255],
            [['msg_pers_email'], 'email', 'except' => ['importdata']],
            [['employer', 'asker', 'askid', 'askcontacts', 'tags'], 'string', 'max' => 255],
            [['tagsstring'], 'string', 'max' => 1024],
            [['msg_empl_id', 'msg_empl_command'], 'required',
                'when' => function($model) use ($aFlagsToAnswer) { return ($this->scenario != 'importdata') && in_array($this->msg_flag, $aFlagsToAnswer); },
                'whenClient' => "function (attribute, value) { return [".implode(',', $aFlagsToAnswer)."].indexOf(parseInt($('#".Html::getInputId($this, 'msg_flag') ."').val())) != -1 ;}"
            ]
        ];
    }

    public function setupEkisData($attribute, $params) {
        $ob = $this->getEkisOrgData($this->ekis_id);
        if( $ob !== null ) {
            $sOld = "{$this->msg_pers_org} + {$this->msg_pers_region}";
            $this->msg_pers_org = $ob['text'];
            $this->msg_pers_region = $ob['eo_district_name_id'];
            Yii::info("setupEkisData({$this->ekis_id}): {$sOld} -> {$this->msg_pers_org} + {$this->msg_pers_region}");
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
//                'alltags',
                'tagsstring',
                'msg_answertime',
            ]
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
                'form' => '_form'
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
            'msg_pers_region' => 'Район',
            'msg_pers_text' => 'Обращение',
            'msg_comment' => 'Комментарий',
            'msg_empl_id' => 'Ответчик',
            'msg_empl_command' => 'Поручение ответчику',
            'msg_empl_remark' => 'Замечание ответчику',
            'msg_answer' => 'Ответ',
            'msg_answertime' => 'Дата ответа',
            'msg_oldcomment' => 'Старые теги',
            'msg_flag' => 'Состояние',
            'msg_subject' => 'Тема',
            'ekis_id' => 'Учреждение',
//            'fl_hint' => 'Описание',

            'employer' => 'Ответчик',
            'asker' => 'Проситель',
            'answers' => 'Соответчики',
            'askid' => 'Номер и дата',
            'askcontacts' => 'Контакты',
            'tags' => 'Теги',
            'alltags' => 'Теги',
            'file' => 'Файл',
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
     * Отношения к Ответчику
     *
     */
    public function getEmployee() {
        return $this->hasOne(User::className(), ['us_id' => 'msg_empl_id']);
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
     *  Связь с табличкой, соединяющей сообщения и его соответчиков
     */
    public function getUsers() {
        return $this->hasMany(
            Msganswers::className(),
            ['ma_message_id' => 'msg_id']
        );
    }

    /**
     *  Связь сообщения и его ответчиков
     */
    public function getAnswers() {
        return $this
            ->hasMany(
                User::className(),
                ['us_id' => 'ma_user_id'])
            ->via('users');
    }

    /**
     *  Установка соответчиков
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
     *  Связь сообщения и его тегов строкой с разделителями ','
     */
    public function getTagsstring() {
        return implode(',', ArrayHelper::map($this->alltags, 'tag_id', 'tag_title'));
    }

    /**
     *  Установка тегов по строке с разделителями ','
     * @param string $val
     */
    public function setTagsstring($val) {
        $a = explode(',', $val);
        foreach($a As $k => $v) {
            $v = trim($v);
            if( $v === '' ) {
                unset($a[$k]);
            }
        }
        $this->_tagsstring = $a;
    }

    /**
     *  Получение всех ответчиков
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
     *  Возможность ответа
     */
    public function getIsAnswerble() {
        $aFlagAns = [
            Msgflags::MFLG_SHOW_INSTR,
            Msgflags::MFLG_INT_INSTR,
            Msgflags::MFLG_SHOW_REVIS,
            Msgflags::MFLG_INT_REVIS_INSTR,
        ];
        $bRet = ((Yii::$app->user->can(Rolesimport::ROLE_ANSWER_DOGM)
             && $this->msg_empl_id == Yii::$app->user->identity->getId())
             || Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM))
             && in_array($this->msg_flag, $aFlagAns)
             ;
        return $bRet;
    }

    /**
     *  Сохраняем соответчиков
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
     * @param string $sType тип данных для возврата: флаги для отправки уведомлений по пользователям, ответчикам и соответчикам
     * @return array массив переходов флагов: ключ - текущий флаг, значение - массив флагов, при переходе из которых будет присходить отправка писем
     *
     */
    public function getTransflags($sType = '')
    {
        $transTable = [
            self::USERTYPE_PERSON => [
                Msgflags::MFLG_SHOW_NO_ANSWER => [Msgflags::MFLG_NEW],
                Msgflags::MFLG_SHOW_INSTR => [Msgflags::MFLG_NEW],
                Msgflags::MFLG_SHOW_ANSWER => [Msgflags::MFLG_SHOW_INSTR, Msgflags::MFLG_SHOW_NEWANSWER, Msgflags::MFLG_SHOW_REVIS,],
                Msgflags::MFLG_INT_FIN_INSTR => [],
            ],
            self::USERTYPE_ANSWER => [
                Msgflags::MFLG_SHOW_INSTR => [],
                Msgflags::MFLG_SHOW_REVIS => [],
                Msgflags::MFLG_INT_INSTR => [],
                Msgflags::MFLG_INT_REVIS_INSTR => [],
                Msgflags::MFLG_NEW => [
                    Msgflags::MFLG_SHOW_INSTR,
                    Msgflags::MFLG_SHOW_REVIS,
                    Msgflags::MFLG_INT_INSTR,
                    Msgflags::MFLG_INT_REVIS_INSTR,
                    Msgflags::MFLG_INT_NEWANSWER,
                    Msgflags::MFLG_SHOW_NEWANSWER,
                ],
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
     * @param string $sType тип данных для возврата: флаги для отправки уведомлений по пользователям, ответчикам и соответчикам
     * @return boolean надо ли отправлять уведомление
     *
     */
    public function isNeedNotificate($sType = '') {
        $bRet = $this->isFlagChanged();
        Yii::info('isNeedNotificate('.$sType.') flag ' . ($bRet ? '' : 'not ') . 'changed');

        if( $bRet ) {
            $transTable = $this->getTransflags($sType);
            if( !isset($transTable[$this->msg_flag])
             || ( !empty($transTable[$this->msg_flag]) && !in_array($this->_oldAttributes['msg_flag'], $transTable[$this->msg_flag]))
            ) {
                $bRet = false;
                Yii::info('isNeedNotificate('.$sType.') = false [' . implode(',', (isset($transTable[$this->msg_flag]) && is_array($transTable[$this->msg_flag])) ? $transTable[$this->msg_flag] : []) . '] ' . $this->_oldAttributes['msg_flag'] . ' -> ' . $this->msg_flag);
            }
            else {
                Yii::info('isNeedNotificate('.$sType.') = true [' . implode(',', $transTable[$this->msg_flag]) . '] ' . $this->_oldAttributes['msg_flag'] . ' -> ' . $this->msg_flag);
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
                Msgflags::MFLG_SHOW_ANSWER => 'user_notif_answer',
                Msgflags::MFLG_INT_FIN_INSTR => 'user_notif_intanswer',
            ],
            self::USERTYPE_ANSWER => [
                Msgflags::MFLG_SHOW_INSTR => 'ans_notif_instr',
                Msgflags::MFLG_INT_INSTR => 'ans_notif_instr',
                Msgflags::MFLG_SHOW_REVIS => 'ans_notif_revis',
                Msgflags::MFLG_INT_REVIS_INSTR => 'ans_notif_revis',
                Msgflags::MFLG_NEW => 'ans_notif_esc',
            ],
            self::USERTYPE_SOANSWER => [
                Msgflags::MFLG_SHOW_INSTR => 'soans_notif_instr',
                Msgflags::MFLG_INT_INSTR => 'soans_notif_instr',
            ]
        ];

        foreach($aType As $sType) {
            if ($this->isNeedNotificate($sType)) {
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
                } // switch ($sType)
            } // if ($this->isNeedNotificate($sType))
        } // foreach($aType As $sType)

        if( count($aMessages) > 0 ) {
            Yii::$app->mailer->sendMultiple($aMessages);
        }
    }

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
                'fields' => implode(";", ["eo_id", "eo_short_name", "eo_district_name_id"]),
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
}
