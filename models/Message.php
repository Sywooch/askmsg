<?php

namespace app\models;

use Yii;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeBehavior;
use yii\base\Event;
use yii\helpers\ArrayHelper;
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
 * @property string $fl_hint
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

    public $employer; // Ответчик
    public $asker; // Проситель
    public $askid; // Номер и дата
    public $askcontacts; // Email и телефон
    public $tags; // округ, комментарии
    /**
     * @var mixed file аттрибут для генерации поля добавления файла
     */
    public $file;
    public $attachfile;

    public $_oldAttributes = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     *
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
                Msgflags::MFLG_INT_FIN_INSTR,
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
     *
     */
    public static function getNotificationFlags($sUserRole)
    {
        // Флаги сообщений для разных пользователей
        $_flagFilter = [
            Rolesimport::ROLE_GUEST => [
                Msgflags::MFLG_THANK,
                Msgflags::MFLG_SHOW_NO_ANSWER,
                Msgflags::MFLG_SHOW_ANSWER,
                Msgflags::MFLG_SHOW_INSTR,
            ],
            Rolesimport::ROLE_MODERATE_DOGM => [
                Msgflags::MFLG_NEW,
                Msgflags::MFLG_INT_NEWANSWER,
                Msgflags::MFLG_SHOW_NEWANSWER,
            ],
        ];
        return isset($_flagFilter[$sUserRole]) ? $_flagFilter[$sUserRole] : [];
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
                    return mb_strtoupper(mb_substr($s, 0, 1)) . mb_substr($s, 1);
                },
            ],
        ];

        if( $this->scenario != 'importdata' ) {
            $a = array_merge(
                $a,
                // поставим флаг нового сообщения
                [
                    [
                        'class' => AttributeBehavior::className(),
                        'attributes' => [
                            ActiveRecord::EVENT_BEFORE_INSERT => 'msg_flag',
                        ],
                        'value' => function ($event) {
                            return Msgflags::MFLG_NEW;
                        },
                    ],
                    // поставим флаг активности сообщения
                    [
                        'class' => AttributeBehavior::className(),
                        'attributes' => [
                            ActiveRecord::EVENT_BEFORE_INSERT => 'msg_active',
                        ],
                        'value' => function ($event) {
                            return 1;
                        },

                    ],
                    // сохраним предыдущие аттрибуты
                    [
                        'class' => AttributeBehavior::className(),
                        'attributes' => [
                            ActiveRecord::EVENT_AFTER_FIND => '_oldAttributes',
                        ],
                        'value' => function ($event) {
                            return [
                                'msg_flag' => $event->sender->msg_flag,
                            ];
                        },

                    ],
                    // отправим оповещения
                    [
                        'class' => NotificateBehavior::className(),
                        'allevents' => [
                            ActiveRecord::EVENT_AFTER_INSERT,
                            ActiveRecord::EVENT_AFTER_UPDATE,
                        ],
                        'value' => function ($event, $model) {
                            /** @var $model Message */
                            Yii::$app->cache->delete(Message::KEY_STATMSG_DATA); // удалим статистику
                            if( !isset($model->_oldAttributes['msg_flag'])
                                || ( isset($model->_oldAttributes['msg_flag'])
                                    && $model->_oldAttributes['msg_flag'] != $model->msg_flag )
                                && in_array($model->msg_flag, $model->getNotificationFlags(Rolesimport::ROLE_GUEST)) ) {

                                Yii::$app->mailer->compose('notificateUser', ['model' => $model, 'flag' => isset($model->_oldAttributes['msg_flag']) ? $model->_oldAttributes['msg_flag'] : 0])
                                    ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                                    ->setTo($model->msg_pers_email)
                                    ->setSubject('Обращение №' . $model->msg_id)
                                    ->send();

                            }
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
        return [
            [['msg_pers_name', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_pers_text', 'msg_pers_region'], 'required'],
            [['msg_answer'], 'required', 'on' => 'answer', ],
            [['msg_pers_org', 'ekis_id', 'msg_subject', 'msg_pers_secname'], 'required', 'on'=>'person', ],
//            [['msg_pers_secname'], 'required', 'on'=>['answer', 'person', 'moderator']],
            [['msg_createtime', 'msg_answertime'], 'filter', 'filter' => function($v){ return empty($v) ? new Expression('NOW()') : $v; }],
            [['msg_createtime', 'msg_answertime'], 'safe'],
            [['msg_flag'], 'required'],
//            [['answers'], 'safe'],
            [['answers'], 'in', 'range' => array_keys(User::getGroupUsers(Rolesimport::ROLE_12, '', '{{val}}')), 'allowArray' => true],
            [['alltags'], 'in', 'range' => array_keys(ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_TAG), 'tag_id', 'tag_title')), 'allowArray' => true],
            [['file'], 'safe'],
            [['file'], 'file', 'maxFiles' => $fileCount, 'maxSize' => Yii::$app->params['message.file.maxsize'], 'extensions' => Yii::$app->params['message.file.ext']],
            [['attachfile'], 'safe'],
            [['attachfile'], 'file', 'maxFiles' => $fileCount, 'maxSize' => Yii::$app->params['message.file.maxsize'], 'extensions' => Yii::$app->params['message.file.ext']],
//            [['answers'], 'in', 'range' => array_keys(User::getGroupUsers(Rolesimport::ROLE_ANSWER_DOGM, '', '{{val}}')), 'allowArray' => true],
            [['msg_id', 'msg_active', 'msg_pers_region', 'msg_empl_id', 'msg_flag', 'msg_subject', 'ekis_id'], 'integer'],
            [['msg_pers_text'], 'string', 'max' => self::MAX_PERSON_TEXT_LENGTH, 'on' => 'person'],
            [['msg_answer', 'msg_empl_command', 'msg_empl_remark', 'msg_comment', 'msg_pers_org', 'fl_hint'], 'string'],
            [['msg_answer'], 'filter', 'filter' => function($v){ return strip_tags($v, '<p><a><li><ol><ul><strong><b><em><i><u><h1><h2><h3><h4><h5><blockquote><pre><del><br>');  }],
            [['msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_oldcomment'], 'string', 'max' => 255],
            [['employer', 'asker', 'askid', 'askcontacts', 'tags'], 'string', 'max' => 255],
        ];
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
            'attachfile',
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
                                        'alltags',
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

        $scenarios['answer'] = ['msg_answer', 'msg_answertime', 'msg_flag', 'file', 'attachfile'];

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
            'msg_id' => 'ID',
            'msg_createtime' => 'Создано',
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
            'fl_hint' => 'Описание',

            'employer' => 'Ответчик',
            'asker' => 'Проситель',
            'answers' => 'Соответчики',
            'askid' => 'Номер и дата',
            'askcontacts' => 'Контакты',
            'tags' => 'Теги',
            'alltags' => 'Теги',
            'file' => 'Файл',
            'attachfile' => 'Прикрепить',
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
     * Отношения к теме
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
//        Yii::info('saveAlltags: ' . print_r($model, true));
        $model->saveRelateddata([
            'eventname' => $event->name,
            'reltableclass' => Msgtags::className(),
            'msgidfield' => 'mt_msg_id',
            'relateidfield' => 'mt_tag_id',
            'relateidarray' => $model->alltags,
        ]);
    }

    /**
     *  Сохраняем соответствующие данные
     * @param array $param
     */
    public function saveRelateddata($param) {
//        Yii::info('saveRelateddata() ' . print_r($param, true));
        if( $param['eventname'] === ActiveRecord::EVENT_AFTER_UPDATE ) {
            $nCou = $param['reltableclass']::updateAll([$param['msgidfield'] => 0, $param['relateidfield'] => 0], $param['msgidfield'] . ' = ' . $this->msg_id);
//            Yii::info('Clear relate records: ' . $nCou);
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
//                else {
//                    Yii::info('Update relate records : ['.$this->msg_id.', '.$id.']');
//                }
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
        Yii::info("uploadFiles() files = " . count($files));

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

}
