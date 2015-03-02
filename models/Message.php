<?php

namespace app\models;

use Yii;
use app\models\User;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

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
    public $employer; // Ответчик
    public $asker; // Проситель
    public $askid; // Номер и дата
    public $askcontacts; // Email и телефон
    public $tags; // округ, контакты

    public $_oldAttributes = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors(){
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'msg_createtime',
                'updatedAtAttribute' => null,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // , 'ekis_is'
            [['msg_pers_name', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_pers_text', 'msg_pers_org', 'msg_pers_region'], 'required'],
            [['msg_answer'], 'required'],
            [['msg_pers_secname'], 'required', 'on'=>['answer', 'person', 'moderator']],
            [['msg_createtime', 'msg_answertime'], 'filter', 'filter' => function($v){ return empty($v) ? new Expression('NOW()') : $v;  }],
            [['msg_createtime', 'msg_answertime'], 'safe'],
            [['msg_active', 'msg_pers_region', 'msg_empl_id', 'msg_flag'], 'integer'],
            [['msg_pers_text', 'msg_answer', 'msg_empl_command', 'msg_empl_remark', 'msg_comment', 'msg_pers_org'], 'string'],
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
        $scenarios['person'] = ['msg_pers_name', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_pers_text', 'msg_pers_secname', 'msg_pers_org', 'msg_pers_region', 'msg_createtime'];
        $scenarios['answer'] = ['msg_answer', 'msg_answertime'];
        $scenarios['moderator'] = ['msg_empl_command', 'msg_empl_remark', 'msg_comment', 'msg_empl_id', 'msg_flag', 'msg_active'];

        return $scenarios;
    }

    public function getScenariosData()
    { // TODO: отправить это все в конфиг
        $a = [
            'person' => ['title' => 'Создать обращение', 'form' => '_form'],
            'answer' => ['title' => 'Написать ответ', 'form' => '_formanswer'],
            'moderator' => ['title' => 'Направить обращение', 'form' => '_formmoderator'],
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
            'msg_empl_command' => 'Распоряжение ответчику',
            'msg_empl_remark' => 'Замечание ответчику',
            'msg_answer' => 'Ответ',
            'msg_answertime' => 'Дата ответа',
            'msg_oldcomment' => 'Старые теги',
            'msg_flag' => 'Флаг обращения',

            'employer' => 'Ответчик',
            'asker' => 'Проситель',
            'askid' => 'Номер и дата',
            'askcontacts' => 'Контакты',
            'tags' => 'Теги',
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



}
