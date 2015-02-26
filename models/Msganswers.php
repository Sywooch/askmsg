<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%msganswers}}".
 *
 * @property integer $ma_id
 * @property integer $ma_message_id
 * @property integer $ma_user_id
 */
class Msganswers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%msganswers}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ma_message_id', 'ma_user_id'], 'required'],
            [['ma_message_id', 'ma_user_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ma_id' => 'Ma ID',
            'ma_message_id' => 'Ma Message ID',
            'ma_user_id' => 'Ma User ID',
        ];
    }
}
