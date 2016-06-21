<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%appeal}}".
 *
 * @property integer $ap_id
 * @property string $ap_created
 * @property string $ap_next_act_date
 * @property string $ap_pers_name
 * @property string $ap_pers_secname
 * @property string $ap_pers_lastname
 * @property string $ap_pers_email
 * @property string $ap_pers_phone
 * @property string $ap_pers_org
 * @property string $ap_pers_region
 * @property string $ap_pers_text
 * @property string $ap_empl_command
 * @property string $ap_comment
 * @property integer $ap_subject
 * @property integer $ap_empl_id
 * @property integer $ap_curator_id
 * @property integer $ekis_id
 * @property integer $ap_state
 * @property integer $ap_ans_state
 */
class Appeal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%appeal}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ap_created', 'ap_next_act_date'], 'safe'],
            [['ap_pers_text', 'ap_empl_command', 'ap_comment'], 'string'],
            [['ap_subject', 'ap_empl_id', 'ap_curator_id', 'ekis_id', 'ap_state', 'ap_ans_state'], 'integer'],
            [['ap_pers_name', 'ap_pers_secname', 'ap_pers_lastname', 'ap_pers_org', 'ap_pers_region'], 'string', 'max' => 255],
            [['ap_pers_email'], 'string', 'max' => 128],
            [['ap_pers_phone'], 'string', 'max' => 24]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ap_id' => 'Номер',
            'ap_created' => 'Создано',
            'ap_next_act_date' => 'Срок действия',
            'ap_pers_name' => 'Имя',
            'ap_pers_secname' => 'Отчество',
            'ap_pers_lastname' => 'Фамилия',
            'ap_pers_email' => 'Email',
            'ap_pers_phone' => 'Телефон',
            'ap_pers_org' => 'Учреждение',
            'ap_pers_region' => 'Округ',
            'ap_pers_text' => 'Обращение',
            'ap_empl_command' => 'Поручение исполнителю',
            'ap_comment' => 'Комментарий',
            'ap_subject' => 'Тема',
            'ap_empl_id' => 'Исполнитель',
            'ap_curator_id' => 'Контролер',
            'ekis_id' => 'Учреждение',
            'ap_state' => 'Состояние',
            'ap_ans_state' => 'Состояние ответа',
        ];
    }
}
