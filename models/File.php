<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%file}}".
 *
 * @property integer $file_id
 * @property string $file_time
 * @property string $file_orig_name
 * @property integer $file_msg_id
 * @property integer $file_user_id
 * @property integer $file_size
 * @property string $file_type
 * @property string $file_name
 */
class File extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_time'], 'safe'],
            [['file_orig_name', 'file_size', 'file_name'], 'required'],
            [['file_msg_id', 'file_user_id', 'file_size'], 'integer'],
            [['file_orig_name', 'file_type', 'file_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file_id' => 'File ID',
            'file_time' => 'File Time',
            'file_orig_name' => 'File Orig Name',
            'file_msg_id' => 'File Msg ID',
            'file_user_id' => 'File User ID',
            'file_size' => 'File Size',
            'file_type' => 'File Type',
            'file_name' => 'File Name',
        ];
    }
}
