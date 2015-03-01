<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;
use app\models\Group;

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
class User extends ActiveRecord  implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_WAIT = 0;
    const STATUS_ACTIVE = 1;

    public static $_model = null;
    public $selectedGroups = null;

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
        \Yii::info("rules: " . print_r(array_keys(Group::getActiveGroups()), true));
        return [
            [['us_xtime', 'us_logintime', 'us_regtime', 'us_checkwordtime'], 'safe'],
            [['us_login', 'us_password_hash', 'us_name', 'us_email', 'us_password_hash', 'selectedGroups'], 'required'],
            [['us_active'], 'integer'],
            [['selectedGroups'], 'in', 'range' => array_keys(Group::getActiveGroups()), 'allowArray' => true ],
            [['us_login', 'us_password_hash', 'us_chekword_hash', 'us_name', 'us_secondname', 'us_lastname', 'us_email', 'us_workposition', 'email_confirm_token', 'password_reset_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32]
        ];
    }

    /**
     * Поля для проверки в разных сценариях
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['us_login', 'us_name', 'us_secondname', 'us_lastname', 'us_email', 'us_workposition',
                                'us_active', 'selectedGroups'];
        $scenarios['update'] = array_merge($scenarios['create'], []);
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'us_id' => 'ID',
            'us_xtime' => 'Us Xtime',
            'us_login' => 'Логин',
            'us_password_hash' => 'Password Hash',
            'us_chekword_hash' => 'Chekword Hash',
            'us_active' => 'Активен',
            'us_name' => 'Имя',
            'us_secondname' => 'Отчество',
            'us_lastname' => 'Фамилия',
            'us_email' => 'Email',
            'us_logintime' => 'Дата логина',
            'us_regtime' => 'Дата регистрации',
            'us_workposition' => 'Должность',
            'us_checkwordtime' => 'Дата контрольного слова',
            'auth_key' => 'Auth Key',
            'email_confirm_token' => 'Email Confirm Token',
            'password_reset_token' => 'Password Reset Token',

            'selectedGroups' => 'Группы',
        ];
    }

    /**
     *
     */
    public function getUsergroup() {
        return $this->hasMany(
                Usergroup::className(),
                ['usgr_uid' => 'us_id']
            );
    }

    /**
     *
     */
    public function getPermissions() {
//        return $this->hasMany(Answer::className(), ['id' => 'answer_id'])
//            ->via('questionAnswers'); // Имя связи которая объявлена выше
        return $this
            ->hasMany(
                Group::className(),
                ['group_id' => 'usgr_gid'])
            ->via('usergroup');
    }

    /**
     *
     */
    public function getArrayGroups() {
        if( $this->selectedGroups === null ) {
            $this->selectedGroups = ArrayHelper::getColumn($this->usergroup, 'usgr_gid');
        }
        return $this->selectedGroups;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        if( static::$_model == null ) {
            static::$_model = static::findOne(['us_id' => $id, 'us_active' => self::STATUS_ACTIVE]);
        }
        return static::$_model;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['us_login' => $username, 'us_active' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'us_active' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->us_password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->us_password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @param string $email_confirm_token
     * @return static|null
     */
    public static function findByEmailConfirmToken($email_confirm_token)
    {
        return static::findOne(['email_confirm_token' => $email_confirm_token, 'us_active' => self::STATUS_WAIT]);
    }

    /**
     * Generates email confirmation token
     */
    public function generateEmailConfirmToken()
    {
        $this->email_confirm_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Removes email confirmation token
     */
    public function removeEmailConfirmToken()
    {
        $this->email_confirm_token = null;
    }

}
