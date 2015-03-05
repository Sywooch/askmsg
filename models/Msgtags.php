<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%msgtags}}".
 *
 * @property integer $mt_id
 * @property integer $mt_msg_id
 * @property string $mt_tag_id
 */
class Msgtags extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%msgtags}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mt_msg_id'], 'integer'],
            [['mt_tag_id'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mt_id' => 'Mt ID',
            'mt_msg_id' => 'Mt Msg ID',
            'mt_tag_id' => 'Mt Tag ID',
        ];
    }
}
