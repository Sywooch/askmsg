<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%group}}".
 *
 * @property integer $group_id
 * @property integer $group_active
 * @property string $group_name
 * @property string $group_description
 */
class Group extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%group}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_active'], 'integer'],
            [['group_name', 'group_description'], 'required'],
            [['group_name', 'group_description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'group_id' => 'Group ID',
            'group_active' => 'Group Active',
            'group_name' => 'Group Name',
            'group_description' => 'Group Description',
        ];
    }
}
