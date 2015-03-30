<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%support}}".
 *
 * @property integer $sup_id
 * @property string $sup_createtime
 * @property string $sup_message
 * @property integer $sup_empl_id
 * @property integer $sup_active
 */
class Support extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%support}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sup_createtime'], 'safe'],
            [['sup_message', 'sup_empl_id'], 'required'],
            [['sup_message'], 'string'],
            [['sup_empl_id', 'sup_active'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sup_id' => 'Sup ID',
            'sup_createtime' => 'Sup Createtime',
            'sup_message' => 'Sup Message',
            'sup_empl_id' => 'Sup Empl ID',
            'sup_active' => 'Sup Active',
        ];
    }
}
