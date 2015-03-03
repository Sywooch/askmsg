<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%regions}}".
 *
 * @property integer $reg_id
 * @property string $reg_name
 * @property integer $reg_active
 */
class Regions extends \yii\db\ActiveRecord
{
    public static $_aListData = null;
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

    /**
     * @inheritdoc
     */
    public static function getListData()
    {
        if( self::$_aListData === null ) {
            self::$_aListData = ArrayHelper::map(
                self::find()
                    ->where(['reg_active'=>1])
                    ->orderBy(['reg_name' => SORT_ASC])
                    ->all(),
                'reg_id',
                'reg_name'
            );
        }
        return self::$_aListData;
    }

}
