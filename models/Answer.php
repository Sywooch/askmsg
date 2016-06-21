<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%answer}}".
 *
 * @property integer $ans_id
 * @property string $ans_created
 * @property string $ans_text
 * @property string $ans_remark
 * @property integer $ans_type
 * @property integer $ans_state
 * @property integer $ans_ap_id
 * @property integer $ans_us_id
 * @property integer $ans_mark
 * @property string $ans_mark_comment
 */
class Answer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%answer}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ans_created'], 'safe'],
            [['ans_text', 'ans_remark', 'ans_mark_comment'], 'string'],
            [['ans_type', 'ans_state', 'ans_ap_id', 'ans_us_id', 'ans_mark'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ans_id' => 'Номер',
            'ans_created' => 'Создан',
            'ans_text' => 'Ответ',
            'ans_remark' => 'Замечание',
            'ans_type' => 'Вид ответа',
            'ans_state' => 'Состояние',
            'ans_ap_id' => 'Обращение',
            'ans_us_id' => 'Ответчик',
            'ans_mark' => 'Оценка ответа',
            'ans_mark_comment' => 'Комментарий к оценке',
        ];
    }
}
