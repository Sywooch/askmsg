<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeBehavior;
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
    public $newPassword = null; // используется в поведении passwordBehavior

    public function behaviors()
    {
        if( $this->scenario == 'importdata' ) {
            return [];
        }
        return [
            // устанавливаем пароль для нового пользователя и отправляем ему письмо
            'passwordBehavior' => [
                'class' => PasswordBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => true,
                ],
                'template' => 'user_create_info',
                'subject' => 'Регистрация на портале ' . Yii::$app->name,
            ],

            'timestampBehavior' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['us_regtime'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => [],
                ],
                'value' => new Expression('NOW()'),
            ],

            // обработка нового пароля
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'newPassword',
                ],
                'value' => function ($event) {
                    /** @var User $model */
                    $model = $event->sender;

                    if( $model->newPassword != '' ) {
                        Yii::error('setNewPassword(): new password = ' . $model->newPassword);

                        $model->setPassword($model->newPassword);
                        $model->generateAuthKey();

                        $model->sendNotificate('user_update_pass', 'Изменение пароля на портале ' . Yii::$app->name, ['model' => $model]);
                    }

                    return $model->newPassword;
                },
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
            [['us_email', 'us_name', 'us_secondname', 'us_lastname', ], 'filter', 'filter' => 'trim'],
            [['us_login', ], 'filter', 'filter' => function($v) { return empty($v) ? $this->getLoginFromEmail() : $v; }],
            [['us_password_hash', 'us_name', 'us_email', 'us_password_hash', 'selectedGroups'], 'required'], // 'us_login',
            [['us_workposition'], 'required', 'on' => ['create', 'update']],
            [['us_secondname', 'us_lastname'], 'required', 'on' => ['create', 'update']],
            [['us_active'], 'integer'],
            [['us_login', 'us_email'], 'unique', 'on' => ['create', 'update']],
            [['selectedGroups'], 'in', 'range' => array_keys(Group::getActiveGroups()), 'allowArray' => true ],
            [['us_login', 'us_password_hash', 'us_chekword_hash', 'us_name', 'us_secondname', 'us_lastname', 'us_email', 'us_workposition', 'email_confirm_token', 'password_reset_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['newPassword'], 'string', 'max' => 32],
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
                                'us_active', 'selectedGroups', 'newPassword'];
        $scenarios['update'] = array_merge($scenarios['create'], []); // 'newPassword'
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

        $scenarios['passwordop'] = [
            'password_reset_token',
            'email_confirm_token',
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
            'newPassword' => 'Новый пароль',
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
        return static::findOne(['auth_key' => $token]);
//        return static::findOne(['access_token' => $token]);

//        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return User|null
     */
    public static function findByUsername($username)
    {
        $ob = static::findOne(['us_login' => $username, 'us_active' => self::STATUS_ACTIVE]);
        if( !$ob ) {
            $ob = static::findOne(['us_email' => $username, 'us_active' => self::STATUS_ACTIVE]);
        }
        return $ob;
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
        $bRet =false;
        if( preg_match('/^\$2[axy]\$(\d\d)\$[\.\/0-9A-Za-z]{22}/', $this->us_password_hash, $matches) && $matches[1] >= 4 && $matches[1] <= 30) {
            $bRet = Yii::$app->security->validatePassword($password, $this->us_password_hash);
        }
        Yii::warning("validatePassword({$password}): us_id = {$this->us_id} " . ($bRet ? 'yes' : 'no'));
        if( !$bRet ) {
            $bRet = $this->validateOldPassword($password);
            Yii::warning("validateOldPassword({$password}): " . ($bRet ? 'yes' : 'no'));
            if( $bRet ) {
                // Перекодируем пароль новым алгоритмом
                $this->setPassword($password);
                $this->generateAuthKey();
                $this->scenario = 'passwordop';
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
        Yii::warning("validateOldPassword({$password}): salt = {$salt} checkToken = {$checkToken} hash = {$hash} " . (($checkToken==$hash) ? 'yes' : 'no'));
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
        $s = trim($this->us_name . ' ' . $this->us_secondname);
        if( $s == '' ) {
            $s = $this->us_login;
        }
        return $this->us_lastname . ' ' . $s;
    }

    /**
     *  Имя, фамилия пользователя
     */
    public function getShortName() {
        $s = trim($this->us_name . ' ' . $this->us_secondname);
        if( $s == '' ) {
            $s = $this->us_lastname;
        }
        return $s;
    }

    /**
     *
     *  Поиск пользователей по их группе
     *
     * @param integer $idGroup
     * @param string|array $sQuery
     * @param string $format
     * @return array
     *
     */
    public static function getGroupUsers($idGroup, $sQuery = '', $format = '') {
        $sKey = str_replace(["\n", "\r", ' ',], ['', '', '',], $idGroup .  '_' . (is_array($sQuery) ? print_r($sQuery, true): $sQuery) . '_' . $format);
//        Yii::info('getGroupUsers(): sKey = '.$sKey);
        if( isset(self::$_cache[$sKey]) ) {
//            Yii::info('getGroupUsers(): return self::_cache['.$sKey.']');
            return self::$_cache[$sKey];
        }
//        Yii::info('getGroupUsers(): sQuery = ' . print_r($sQuery, true));
//        Yii::info('getGroupUsers(): debug_backtrace = ' . print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 6), true));

        $aWhere = [
            'usgr_gid' => $idGroup
        ];

        if( is_string($sQuery) && ($sQuery !== '') ) {
            $aWhere = [
                'and',
                $aWhere,
//                ['or', ['like', 'us_lastname', $sQuery], ['like', 'us_name', $sQuery], ['like', 'us_secondname', $sQuery]],
                ['like', 'us_lastname', $sQuery]
            ];
//            Yii::info('getGroupUsers(): string aWhere = ' . print_r($aWhere, true));
        }
        else if( is_array($sQuery) ) {
            $aWhere = [
                'and',
                $aWhere,
                $sQuery
            ];
//            Yii::info('getGroupUsers(): array aWhere = ' . print_r($aWhere, true));
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
                    'val' => $ob->getFullName() . ' (' . $ob->us_login . ')',
                    'pos' => $ob->us_workposition,
                ] :
                str_replace(
                    array('{{id}}', '{{val}}', '{{pos}}'),
                    array($ob->us_id, $ob->getFullName() . ($ob->us_lastname == '' ? (' (' . $ob->us_login . ')') : ''), $ob->us_workposition),
                    $format
                );
            }
        );
        self::$_cache[$sKey] = $aData;
        return $aData;
    }

    /**
     * Отправка письма пользователю
     *
     * @param string $template имя шаблона письма
     * @param string $subject тема письма
     * @param array $data данные для письма
     */
    public function sendNotificate($template, $subject = '', $data = []) {
        if( $subject === '' ) {
            $subject = 'Уведомление портала ' . Yii::$app->name;
        }
        Yii::$app->mailer->compose($template, ['model' => $this,])
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($this->us_email)
            ->setSubject($subject)
            ->send();
    }


    /**
     * Создание login из email
     * @param string $sEmail
     * @return string
     */
    public function getLoginFromEmail($sEmail = '') {
        if( $sEmail == '' ) {
            list($sEmail) = explode('@', $this->us_email);
        }
        if( $sEmail == '' ) {
            $sEmail = 'user';
        }
        $n = 1;
        $login = $sEmail;
        while( true ) {
            $sSql = 'Select COUNT(*) From ' . self::tableName() . ' Where us_login = :login';
            if( Yii::$app->db->createCommand($sSql, [':login' => $login])->queryScalar() > 0 ) {
                $login = $sEmail . '-' . sprintf("%d", $n);
                $n++;
            }
            else {
                break;
            }
        }
        return $login;
    }
}
