<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

use app\models\Group;
use app\components\PasswordBehavior;

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
    public static $_cache = [];
    public $selectedGroups = null;

    public function behaviors()
    {
        if( $this->scenario == 'importdata' ) {
            return [];
        }
        return [
            'passwordBehavior' => [
                'class' => PasswordBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => true,
                ]
            ],
            'timestampBehavior' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['us_regtime'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => [],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }


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
//        \Yii::info("rules: " . print_r(array_keys(Group::getActiveGroups()), true));
        return [
            [['us_xtime', 'us_logintime', 'us_regtime', 'us_checkwordtime'], 'safe'],
            [['us_login', 'us_password_hash', 'us_name', 'us_email', 'us_password_hash', 'selectedGroups'], 'required'],
            [['us_workposition'], 'required', 'on' => ['create', 'update']],
            [['us_secondname', 'us_lastname'], 'required', 'on' => ['create', 'update']],
            [['us_active'], 'integer'],
            [['us_login', 'us_email'], 'unique', 'on' => 'create'],
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
        $scenarios['importdata'] = [
            'us_login',
            'us_name',
            'us_secondname',
            'us_lastname',
            'us_email',
            'us_workposition',
            'us_active',
            'us_regtime',
            'us_logintime',
            'us_checkwordtime',
            'us_chekword_hash',
            'us_password_hash',
        ];
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
     *  Связь с табличкой, соединяющей пользователя и его группы
     */
    public function getUsergroup() {
        return $this->hasMany(
                Usergroup::className(),
                ['usgr_uid' => 'us_id']
            );
    }

    /**
     *  Связь пользователя и его групп
     */
    public function getPermissions() {
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
        if( static::$_model === null ) {
            static::$_model = static::find()
                ->where(['us_id' => $id, 'us_active' => self::STATUS_ACTIVE])
                ->with('permissions')
                ->one();
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
        $bRet = Yii::$app->security->validatePassword($password, $this->us_password_hash);
        if( !$bRet ) {
            $bRet = $this->validateOldPassword($password);
            if( $bRet ) {
                // Перекодируем пароль новым алгоритмом
                $this->setPassword($password);
                $this->generateAuthKey();
                if( !$this->save() ) {
                    Yii::error("Can't save new password " . print_r($this->getErrors(), true));
                }
            }
        }
        return $bRet;
    }

    /**
     * Validates old password
     *     private function bitrix_check_hash($password,$hash){
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validateOldPassword($password)
    {
        $hash = $this->us_password_hash;
        $salt = substr($hash, 0, 8);
        $checkToken = $salt . md5($salt . $password);
        return ($checkToken==$hash);
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

    /**
     *  Полное имя пользователя
     */
    public function getFullName() {
        return $this->us_lastname . ' ' . $this->us_name . ' ' . $this->us_secondname;
    }

    /**
     *
     *  Поиск пользователей по их группе
     *
     * @param integer $idGroup
     * @param string $sQuery
     * @param string $format
     * @return array
     *
     */
    public static function getGroupUsers($idGroup, $sQuery = '', $format = '') {
        $sKey = $idGroup .  $sQuery . $format;
        if( isset(self::$_cache[$sKey]) ) {
            return self::$_cache[$sKey];
        }

        $aWhere = ['usgr_gid' => $idGroup];
        if( $sQuery !== '' ) {
            $aWhere = array_merge(
                ['and'],
                $aWhere,
//                ['or', ['like', 'us_lastname', $sQuery], ['like', 'us_name', $sQuery], ['like', 'us_secondname', $sQuery]],
                ['like', 'us_lastname', $sQuery]
            );
        }

        $aUsers =  User::find()
            ->select(User::tableName() . '.*, ' . Usergroup::tableName() . '.*')
            ->innerJoin(Usergroup::tableName(), 'us_id = usgr_uid')
            ->where($aWhere)
            ->orderBy(['us_lastname' => SORT_ASC, 'us_name' => SORT_ASC, 'us_secondname' => SORT_ASC])
            ->all();

        $aData = ArrayHelper::map(
            $aUsers,
            'us_id',
            function($ob) use ($format) {
                return ( $format === '' ) ? [
                    'id' => $ob->us_id,
                    'val' => $ob->getFullName(),
                    'pos' => $ob->us_workposition,
                ] :
                str_replace(
                    array('{{id}}', '{{val}}', '{{pos}}'),
                    array($ob->us_id, $ob->getFullName(), $ob->us_workposition),
                    $format
                );
            }
        );
        self::$_cache[$sKey] = $aData;
        return $aData;
    }

}
