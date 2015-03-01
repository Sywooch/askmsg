<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

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
    public static $_activeGroups = null;
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
            'group_id' => 'ID',
            'group_active' => 'активно',
            'group_name' => 'Название',
            'group_description' => 'Описание',
        ];
    }

    /**
     *
     */
    public static function setActiveGroups() {
        if( self::$_activeGroups === null ) {
            self::$_activeGroups = Group::find()
                 ->where(['group_active'=>1])
                 ->orderBy(['group_name' => SORT_ASC])
                 ->all();
        }
    }

    /**
     *
     */
    public static function getActiveGroups() {
        self::setActiveGroups();
        return ArrayHelper::map(
                self::$_activeGroups,
                'group_id',
                'group_name'
            );
    }

    /**
     * @param $id
     * @return array
     */
    public static function getGroupById($id) {
        self::setActiveGroups();

        foreach(self::$_activeGroups As $ob) {
            if( $ob->group_id == $id ) {
                return $ob;
            }
        }

        return null;
    }
}
