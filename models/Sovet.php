<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%sovet}}".
 *
 * @property integer $sovet_id
 * @property string $sovet_title
 */
class Sovet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sovet}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sovet_title'], 'required'],
            [['sovet_title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sovet_id' => 'Sovet ID',
            'sovet_title' => 'Назвние',
        ];
    }
}
