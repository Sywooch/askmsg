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
            [['mt_msg_id', 'mt_tag_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mt_id' => 'ID',
            'mt_msg_id' => 'Message ID',
            'mt_tag_id' => 'Tag ID',
        ];
    }
}
