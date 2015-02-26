<?php

namespace app\models;

use Yii;

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
 */
class Message extends \yii\db\ActiveRecord
{
    public $employer; // Ответчик
    public $asker; // Проситель
    public $askid; // Номер и дата

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
    public function rules()
    {
        return [
            // , 'msg_pers_org'
            // , 'ekis_is'
            // , 'msg_pers_secname'
            [['msg_createtime', 'msg_pers_name', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_pers_text'], 'required'],
            [['msg_createtime', 'msg_answertime'], 'safe'],
            [['msg_active', 'msg_pers_region', 'msg_empl_id', 'msg_flag'], 'integer'],
            [['msg_pers_text', 'msg_answer', 'msg_empl_command', 'msg_empl_remark', 'msg_comment', 'msg_pers_org'], 'string'],
            [['msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_oldcomment'], 'string', 'max' => 255],

            [['employer', 'asker'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'msg_id' => 'ID',
            'msg_createtime' => 'Создано',
            'msg_active' => 'Активно',
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
        ];
    }

    /*
     * Отношения к Ответчику
     *
     */
    public function getEmployee() {
        return $this->hasOne(User::className(), ['us_id' => 'msg_empl_id']);
    }

}
