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
    public static function tableNme()
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

    /**
     * Проверяем наличие района в нашей базе
     * @param integer $id
     * @param string $name
     */
    public static function testExistRegion($id, $name)
    {
        $res = Yii::$app->db->createCommand('Select reg_id From '. self::tableNme() . ' Where reg_id = :id', [':id' => $id])->queryOne();
//        Yii::info('Regiond::testExistRegion('.$id.', '.$name.')' . ($res === false ? ' false' : print_r($res, true)));
        if( $res === false ) {
//            Yii::info('Regiond::testExistRegion('.$id.', '.$name.') insert new');
            Yii::$app->db->createCommand('Insert Into '. self::tableNme() . ' (reg_id, reg_name, reg_active) Values (:id, :name, 1)', [':id' => $id, ':name' => $name])->execute();
        }
    }

}
