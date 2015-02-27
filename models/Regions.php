<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%regions}}".
 *
 * @property integer $reg_id
 * @property string $reg_name
 * @property integer $reg_active
 */
class Regions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%regions}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reg_name'], 'required'],
            [['reg_active'], 'integer'],
            [['reg_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'reg_id' => 'Reg ID',
            'reg_name' => 'Reg Name',
            'reg_active' => 'Reg Active',
        ];
    }
}
