<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\Rolesimport;

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
            $aGroups = [Rolesimport::ROLE_ANSWER_DOGM, Rolesimport::ROLE_MODERATE_DOGM];
            Yii::info('setActiveGroups(): property_exists(Yii::app, user) = ' . (property_exists(Yii::$app, 'user') ? 'true' : 'false'));
            if( !property_exists(Yii::$app, 'user') || Yii::$app->user->can(Rolesimport::ROLE_ADMIN) ) {
                $aGroups[] = Rolesimport::ROLE_ADMIN;
            }
            Yii::info('setActiveGroups(): aGroups = ' . print_r($aGroups, true));
            self::$_activeGroups = Group::find()
                 ->where(['group_active'=>1, 'group_id' => $aGroups])
                 ->orderBy(['group_name' => SORT_ASC])
                 ->all();
            Yii::info('setActiveGroups(): self::_activeGroups = ' . print_r(ArrayHelper::map(self::$_activeGroups,'group_id','group_name'), true));
        }
    }

    /**
     *
     */
    public static function getActiveGroups() {
        self::setActiveGroups();
        Yii::info('getActiveGroups(): ' . print_r(ArrayHelper::map(self::$_activeGroups,'group_id','group_name'), true));
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
