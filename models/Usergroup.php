<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "educom_usergroup".
 *
 * @property integer $usgr_id
 * @property integer $usgr_uid
 * @property integer $usgr_gid
 */
class Usergroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'educom_usergroup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usgr_uid', 'usgr_gid'], 'required'],
            [['usgr_uid', 'usgr_gid'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'usgr_id' => 'Usgr ID',
            'usgr_uid' => 'Usgr Uid',
            'usgr_gid' => 'Usgr Gid',
        ];
    }
}
