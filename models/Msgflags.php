<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%msgflags}}".
 *
 * @property integer $fl_id
 * @property string $fl_name
 * @property integer $fl_sort
 */
class Msgflags extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%msgflags}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fl_name'], 'required'],
            [['fl_sort'], 'integer'],
            [['fl_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fl_id' => 'Fl ID',
            'fl_name' => 'Fl Name',
            'fl_sort' => 'Fl Sort',
        ];
    }
}
