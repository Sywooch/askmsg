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
            [['sup_message'], 'required'], // , 'sup_empl_id'
            [['sup_message'], 'string'],
            [['sup_empl_id', 'sup_active'], 'integer'],
            [['sup_createtime'], 'filter', 'filter' => function($v){ return empty($v) ? new Expression('NOW()') : $v; }],
            [['sup_empl_id'], 'filter', 'filter' => function($v){ return empty($v) ? Yii::$app->user->id : $v; }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sup_id' => 'id',
            'sup_createtime' => 'Создано',
            'sup_message' => 'Текст',
            'sup_empl_id' => 'Пользователь',
            'sup_active' => 'Актуально',
        ];
    }
}
