<?php

namespace app\models;

use Yii;
use app\models\User;
use app\models\Msgflags;
use app\models\Msganswers;
use app\models\Msgtags;

use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeBehavior;
use yii\base\Event;
use app\models\Rolesimport;
use app\components\AttributewalkBehavior;

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

    public $employer; // Ответчик
    public $asker; // Проситель
    public $askid; // Номер и дата
    public $askcontacts; // Email и телефон
    public $tags; // округ, комментарии

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
    public static function gerMessageFilters()
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
     * @inheritdoc
     */
    public function behaviors(){
        return [
/*
             [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'msg_createtime',
                'updatedAtAttribute' => null,
                'value' => new Expression('NOW()'),
            ],
*/
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
            // поставим флаг нового сообщения
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'msg_flag',
                ],
                'value' => function ($event) {
                    return Msgflags::MFLG_NEW;
                },
            ],
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
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_pers_text', 'msg_pers_org', 'msg_pers_region', 'msg_subject', 'ekis_id'], 'required'],
            [['msg_answer'], 'required'],
//            [['msg_pers_secname'], 'required', 'on'=>['answer', 'person', 'moderator']],
            [['msg_createtime', 'msg_answertime'], 'filter', 'filter' => function($v){ return empty($v) ? new Expression('NOW()') : $v;  }],
            [['msg_createtime', 'msg_answertime'], 'safe'],
            [['msg_flag'], 'required'],
//            [['answers'], 'safe'],
            [['answers'], 'in', 'range' => array_keys(User::getGroupUsers(Rolesimport::ROLE_12, '', '{{val}}')), 'allowArray' => true],
//            [['answers'], 'in', 'range' => array_keys(User::getGroupUsers(Rolesimport::ROLE_ANSWER_DOGM, '', '{{val}}')), 'allowArray' => true],
            [['msg_active', 'msg_pers_region', 'msg_empl_id', 'msg_flag', 'msg_subject', 'ekis_id'], 'integer'],
            [['msg_pers_text'], 'string', 'max' => self::MAX_PERSON_TEXT_LENGTH],
            [['msg_answer', 'msg_empl_command', 'msg_empl_remark', 'msg_comment', 'msg_pers_org'], 'string'],
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
        $scenarios['person'] = ['msg_pers_name', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_pers_text', 'msg_pers_secname', 'msg_pers_org', 'msg_pers_region', 'msg_createtime', 'msg_subject', 'ekis_id'];
        $scenarios['moderator'] = array_merge(
                                    $scenarios['person'],
                                    ['msg_empl_command', 'msg_empl_remark', 'msg_comment', 'msg_empl_id', 'msg_flag', 'msg_active', 'answers']
        );

        $scenarios['importdata'] = ['msg_pers_name', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_pers_text', 'msg_pers_secname', 'msg_pers_org', 'msg_pers_region', 'msg_createtime'];

        // у старых сообщений нет темы, ekis_id
        foreach(['msg_subject', 'ekis_id'] As $v) {
            $n = array_search($v, $scenarios['moderator'], true);
            if( $n !== false ) {
                $scenarios['moderator'][$n];
            }
        }

        $scenarios['answer'] = ['msg_answer', 'msg_answertime', 'msg_flag'];

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
            'msg_pers_org' => 'Школа',
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

            'employer' => 'Ответчик',
            'asker' => 'Проситель',
            'answers' => 'Соответчики',
            'askid' => 'Номер и дата',
            'askcontacts' => 'Контакты',
            'tags' => 'Теги',
            'alltags' => 'Теги',
        ];
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
                ['tags_id' => 'mt_tag_id'])
            ->via('msgtags');
    }

    /**
     *  Установка тегов
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
        $model->saveRelateddata([
            'eventname' => $event->name,
            'reltableclass' => Msgtags::className(),
            'msgidfield' => 'mt_msg_id',
            'relateidfield' => 'mt_tag_id',
            'relateidarray' => $model->alltags,
        ]);
//        Yii::info("event: {$event->name} -> " . print_r($model->answers, true) . print_r($this->answers, true));
/*
        if( $event->name === ActiveRecord::EVENT_AFTER_UPDATE ) {
            $nCou = Msganswers::updateAll(['ma_message_id' => 0, 'ma_user_id' => 0], 'ma_message_id = ' . $model->msg_id);
            Yii::info('Clear soanswers: ' . $nCou);
        }
        if( is_array($model->answers) ) {
            foreach($model->answers As $id) {
                // Msganswers::updateAll();
                $nUpd = Yii::$app
                    ->db
                    ->createCommand('Update ' . Msganswers::tableName() . ' Set ma_message_id = :ma_message_id, ma_user_id = :ma_user_id Where ma_message_id = 0 Limit 1', [':ma_message_id' => $model->msg_id, ':ma_user_id' => $id])
                    ->execute();
                if( $nUpd == 0 ) {
                    $nUpd = Yii::$app
                        ->db
                        ->createCommand('Insert Into ' . Msganswers::tableName() . ' (ma_message_id, ma_user_id) Values (:ma_message_id,  :ma_user_id)', [':ma_message_id' => $model->msg_id, ':ma_user_id' => $id])
                        ->execute();
                }
            }
        }
*/
    }

    /**
     *  Сохраняем соответствующие данные
     * @param array $param
     */
    public function saveRelateddata($param) {
        if( $param['eventname'] === ActiveRecord::EVENT_AFTER_UPDATE ) {
            $nCou = $param['reltableclass']::updateAll([$param['msgidfield'] => 0, $param['relateidfield'] => 0], $param['msgidfield'] . ' = ' . $this->msg_id);
            Yii::info('Clear soanswers: ' . $nCou);
        }
        if( is_array($param['relateidarray']) ) {
            foreach($param['relateidarray'] As $id) {
                // Msganswers::updateAll();
                $nUpd = Yii::$app
                    ->db
                    ->createCommand('Update ' . $param['reltableclass']::tableName() . ' Set '.$param['msgidfield'].' = :ma_message_id, '.$param['relateidfield'].' = :ma_user_id Where '.$param['msgidfield'].' = 0 Limit 1', [':ma_message_id' => $this->msg_id, ':ma_user_id' => $id])
                    ->execute();
                if( $nUpd == 0 ) {
                    Yii::$app
                        ->db
                        ->createCommand('Insert Into ' . $param['reltableclass']::tableName() . ' ('.$param['msgidfield'].', '.$param['relateidfield'].') Values (:ma_message_id,  :ma_user_id)', [':ma_message_id' => $this->msg_id, ':ma_user_id' => $id])
                        ->execute();
                    Yii::info('Insert saveRelateddata : ['.$this->msg_id.', '.$id.']');
                }
                else {
                    Yii::info('Update saveRelateddata : ['.$this->msg_id.', '.$id.']');
                }
            }
        }
    }

}
