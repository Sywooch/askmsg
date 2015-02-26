<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $us_id
 * @property string $us_xtime
 * @property string $us_login
 * @property string $us_password_hash
 * @property string $us_chekword_hash
 * @property integer $us_active
 * @property string $us_name
 * @property string $us_secondname
 * @property string $us_lastname
 * @property string $us_email
 * @property string $us_logintime
 * @property string $us_regtime
 * @property string $us_workposition
 * @property string $us_checkwordtime
 * @property string $auth_key
 * @property string $email_confirm_token
 * @property string $password_reset_token
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['us_xtime', 'us_logintime', 'us_regtime', 'us_checkwordtime'], 'safe'],
            [['us_login', 'us_password_hash', 'us_name', 'us_email', 'us_regtime'], 'required'],
            [['us_active'], 'integer'],
            [['us_login', 'us_password_hash', 'us_chekword_hash', 'us_name', 'us_secondname', 'us_lastname', 'us_email', 'us_workposition', 'email_confirm_token', 'password_reset_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'us_id' => 'Us ID',
            'us_xtime' => 'Us Xtime',
            'us_login' => 'Us Login',
            'us_password_hash' => 'Us Password Hash',
            'us_chekword_hash' => 'Us Chekword Hash',
            'us_active' => 'Us Active',
            'us_name' => 'Us Name',
            'us_secondname' => 'Us Secondname',
            'us_lastname' => 'Us Lastname',
            'us_email' => 'Us Email',
            'us_logintime' => 'Us Logintime',
            'us_regtime' => 'Us Regtime',
            'us_workposition' => 'Us Workposition',
            'us_checkwordtime' => 'Us Checkwordtime',
            'auth_key' => 'Auth Key',
            'email_confirm_token' => 'Email Confirm Token',
            'password_reset_token' => 'Password Reset Token',
        ];
    }
}
