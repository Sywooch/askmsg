<?php

namespace app\models;

use Yii;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeBehavior;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\UploadedFile;

use Httpful\Request;
use Httpful\Response;

use app\models\Stateflag;
use app\components\AttributewalkBehavior;
use app\models\Answer;

/**
 * This is the model class for table "{{%appeal}}".
 *
 * @property integer $ap_id
 * @property string $ap_created
 * @property string $ap_next_act_date
 * @property string $ap_pers_name
 * @property string $ap_pers_secname
 * @property string $ap_pers_lastname
 * @property string $ap_pers_email
 * @property string $ap_pers_phone
 * @property string $ap_pers_org
 * @property string $ap_pers_region
 * @property string $ap_pers_text
 * @property string $ap_empl_command
 * @property string $ap_comment
 * @property integer $ap_subject
 * @property integer $ap_empl_id
 * @property integer $ap_curator_id
 * @property integer $ekis_id
 * @property integer $ap_state
 * @property integer $ap_ans_state
 */
class Appeal extends \yii\db\ActiveRecord
{
    const MAX_PERSON_TEXT_LENGTH = 4000;

    /**
     * @var mixed $file аттрибут для генерации поля добавления файла
     */
    public $file;

    /**
     * @var string $verifyCode для возможной капчи
     */
    public $verifyCode;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%appeal}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors(){
        $a = [
            // сделаем первые буковки имени большими
            [
                'class' => AttributewalkBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ap_pers_name', 'ap_pers_secname', 'ap_pers_lastname'],
                ],
                'value' => function ($event, $attribute) {
                    // тут еще, конечно, можно предусмотреть хитрые имена-фамилии типа Кара-Мурза, но пока не буду
                    /** @var  $model Activerecord */
                    $model = $event->sender;
                    $s = $model->$attribute;
                    $s = mb_strtoupper(mb_substr($s, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($s, 1, 255, 'UTF-8');
//                    Yii::info('Convert letter: ' . $model->$attribute . ' -> ' . $s);
                    return $s;
                },
            ],

            // при добавлении сообщения
            [
                'class' =>  AttributewalkBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ap_state', 'ap_ans_state', 'ap_created'],
                ],
                'value' => function ($event, $attribute) {
                    $aVal = [
                        'ap_state' => Stateflag::STATE_APPEAL_NEW,          // поставим флаг нового сообщения
                        'ap_ans_state' => Stateflag::STATE_ANSWER_NOT_NEED, // поставим флаг ответа
                        'ap_created' => new Expression('NOW()'),
                    ];
                    if( isset($aVal[$attribute]) ) {
                        return $aVal[$attribute];
                    }
                    return null;
                },
            ],

            // сохраним предыдущие аттрибуты
//            [
//                'class' => AttributeBehavior::className(),
//                'attributes' => [
//                    ActiveRecord::EVENT_AFTER_FIND => '_oldAttributes',
//                ],
//                'value' => function ($event) {
//                    /** @var Message $ob */
//                    $ob = $event->sender;
//                    return [
//                        'msg_flag' => $ob->msg_flag,
//                        'answers' => $ob->allanswers,
//                        'msg_empl_id' => $ob->msg_empl_id,
//                        'msg_curator_id' => $ob->msg_curator_id,
//                    ];
//                },
//            ],

        ];

        return $a;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $fileCount = $this->countAvalableFile();

        return [
            [['ap_created', 'ap_next_act_date'], 'safe'],
            [['ap_pers_name', 'ap_pers_secname', 'ap_pers_lastname', ], 'filter', 'filter' => 'trim'],

            [['ap_pers_text', 'ap_empl_command', 'ap_comment'], 'string'],
            [['ap_subject', 'ap_empl_id', 'ap_curator_id', 'ekis_id', 'ap_state', 'ap_ans_state'], 'integer'],
            [['ap_pers_name', 'ap_pers_secname', 'ap_pers_lastname', 'ap_pers_org', 'ap_pers_region'], 'string', 'max' => 255],
            [['ap_pers_email'], 'string', 'max' => 128],
            [['ap_pers_phone'], 'string', 'max' => 24],

            // -------------------------------------------------------------------------

            [['ap_pers_text'], 'filter', 'filter' => function($val){ return strip_tags($val, '<p><br>');  }, ], // в пользовательском вводе удаляем теги
            [['ap_pers_name', 'ap_pers_secname', 'ap_pers_lastname', ], 'filter', 'filter' => function($val){ return strip_tags($val);  }, ],

            [['ekis_id'], 'setupEkisData', ],

            [['ap_pers_name', 'ap_pers_secname', 'ap_pers_lastname', 'ap_pers_email', 'ap_pers_phone', 'ap_pers_text', 'ap_pers_region', 'ap_pers_org', 'ekis_id', 'ap_subject', ], 'required'],

            [['file'], 'safe'],
            [['file'], 'file', 'maxFiles' => $fileCount, 'maxSize' => Yii::$app->params['message.file.maxsize'], 'extensions' => Yii::$app->params['message.file.ext']],

            [['ap_pers_text'], 'string', 'max' => self::MAX_PERSON_TEXT_LENGTH, 'min' => 32, 'tooShort' => 'Напишите более подробное сообщение'],
            [['ap_pers_text'], 'app\components\RustextValidator', 'capital' => 0.2, 'russian' => 0.8, ],

            [['ap_pers_name', 'ap_pers_secname', 'ap_pers_lastname', ], 'match',
                'pattern' => '|^[А-Яа-яЁё]{2}[-А-Яа-яЁё\\s]*$|u', 'message' => 'Допустимы символы русского алфавита',
            ],
            [['ap_pers_name', ], 'filterUserName', ],
            [['ap_pers_phone', ], 'match',
                'pattern' => '|^\\+7\\([\\d]{3}\\)\s+[\\d]{3}-[\\d]{2}-[\\d]{2}$|', 'message' => 'Нужно указать правильный телефон',
            ],

//            ['verifyCode', 'captcha'],

            [['ap_pers_email'], 'email', ],

//            [['employer', 'asker', 'askid', 'askcontacts', 'tags'], 'string', 'max' => 255],
//            [['tagsstring'], 'string', 'max' => 1024],
//            [['msg_empl_id', 'msg_empl_command'], 'required',
//                'when' => function($model) use ($aFlagsToAnswer) { return ($this->scenario != 'importdata') && in_array($this->msg_flag, $aFlagsToAnswer); },
//                'whenClient' => "function (attribute, value) { return [".implode(',', $aFlagsToAnswer)."].indexOf(parseInt($('#".Html::getInputId($this, 'msg_flag') ."').val())) != -1 ;}"
//            ],
//            [['msg_empl_remark'], 'required',
//                'when' => function($model) { return in_array($this->msg_flag, [Msgflags::MFLG_INT_REVIS_INSTR, Msgflags::MFLG_SHOW_REVIS]); },
//                'whenClient' => "function (attribute, value) { return [".implode(',', [Msgflags::MFLG_INT_REVIS_INSTR, Msgflags::MFLG_SHOW_REVIS])."].indexOf(parseInt($('#".Html::getInputId($this, 'msg_flag') ."').val())) != -1 ;}"
//            ]

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ap_id' => 'Номер',
            'ap_created' => 'Создано',
            'ap_next_act_date' => 'Срок действия',
            'ap_pers_name' => 'Имя',
            'ap_pers_secname' => 'Отчество',
            'ap_pers_lastname' => 'Фамилия',
            'ap_pers_email' => 'Email',
            'ap_pers_phone' => 'Телефон',
            'ap_pers_org' => 'Учреждение',
            'ap_pers_region' => 'Округ',
            'ap_pers_text' => 'Обращение',
            'ap_empl_command' => 'Поручение исполнителю',
            'ap_comment' => 'Комментарий',
            'ap_subject' => 'Тема',
            'ap_empl_id' => 'Исполнитель',
            'ap_curator_id' => 'Контролер',
            'ekis_id' => 'Учреждение',
            'ap_state' => 'Состояние',
            'ap_ans_state' => 'Состояние ответа',
        ];
    }

    /**
     * Проверка на одно и тоже слово в полях ФИО
     *
     * @param $attribute
     * @param $params
     */
    public function filterUserName($attribute, $params) {
//        Yii::info('filterUserName('.$attribute.'): ' . $this->ap_pers_name . $this->ap_pers_secname . $this->ap_pers_lastname);
        if( ($this->ap_pers_name == $this->ap_pers_secname)
            && ($this->ap_pers_name == $this->ap_pers_lastname) ) {
            $this->addError($attribute, 'Неправильное имя');
//            Yii::info('filterUserName('.$attribute.'): error');
        }
    }

    public function setupEkisData($attribute, $params) {
        $ob = $this->getEkisOrgData($this->ekis_id);
        if( $ob !== null ) {
            $this->ap_pers_org = $ob['text'];
            $this->ap_pers_region = $ob['eo_district_name_id'];
            Regions::testExistRegion($ob['eo_district_name_id'], $ob['eo_district_name']);
//            Yii::info("setupEkisData({$this->ekis_id}): {$sOld} -> {$this->msg_pers_org} + {$this->msg_pers_region}");
        }
    }

    /**
     * @param $id
     * @return mixed|null
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getEkisOrgData($id) {
        $ob = null;
        if( $id > 0 ) {
            $data = [
                'filters' => [
                    'eo_id' => $id,
                ],
                'maskarade' => [
                    'eo_id' => "id",
                    'eo_short_name' => "text",
                ],
                'fields' => implode(";", ["eo_id", "eo_short_name", "eo_district_name_id", "eo_district_name"]),
            ];
            $request = Request::post('http://hastur.temocenter.ru/task/eo.search/') // , http_build_query($data), 'application/x-www-form-urlencoded'
            ->addHeader('Accept', 'application/json; charset=UTF-8')
                ->body(http_build_query($data))
                ->contentType('application/x-www-form-urlencoded');

            /** @var Response $response */
            $response = $request->send();
            $aData = json_decode($response->body, true);
            if( isset($aData['total']) && ($aData['total'] > 0) ) {
                $ob = array_pop($aData['list']);
            }
        }
        return $ob;
    }

    /**
     * Подсчет возможного количества загружаемых файлов
     *
     * @return int
     */
    public function countAvalableFile() {
        $n = Yii::$app->params['message.file.newcount'];
        if( !$this->isNewRecord ) {
            $n = Yii::$app->params['message.file.answercount'];
            foreach($this->attachments As $ob) {
                /** @var File  $ob */
                if( $ob->file_user_id !== null ) {
                    $n -= 1;
                }
//                Yii::info("countAvalableFile() [{$n}]" . print_r($ob->attributes, true));
            }
            if( $n < 0 ) {
                $n = 0;
            }
        }
//        Yii::info("countAvalableFile() return {$n}");
        return $n;
    }

    /**
     *  Полное имя просителя
     */
    public function getFullName() {
        return $this->ap_pers_lastname . ' ' . $this->ap_pers_name . ' ' . $this->ap_pers_secname;
    }

    /**
     *  Имя просителя без фамилии
     */
    public function getShortName() {
        $s = trim($this->ap_pers_name . ' ' . $this->ap_pers_secname);
        if( $s == '' ) {
            $s = $this->ap_pers_lastname;
        }
        return $s;
    }


    /*
     * Отношения к файлам
     *
     */
    public function getAttachments() {
        return $this->hasMany(
            File::className(),
            ['file_msg_id' => 'ap_id']
        );
    }

    /*
     * Отношения к Исполнителю
     *
     */
    public function getEmployee() {
        return $this->hasOne(User::className(), ['us_id' => 'ap_empl_id']);
    }

    /*
     * Отношения к Куратору
     *
     */
    public function getCurator() {
        return $this->hasOne(User::className(), ['us_id' => 'ap_curator_id']);
    }

    /*
     * Отношения к Региону
     *
     */
    public function getRegion() {
        return $this->hasOne(Regions::className(), ['reg_id' => 'ap_pers_region']);
    }

    /*
     * Отношения к теме
     *
     */
    public function getSubject() {
        return $this->hasOne(Tags::className(), ['tag_id' => 'ap_subject']);
    }

    /**
     *  Связь с табличкой, соединяющей сообщения и его соисполнителей
     */
    public function getUsers() {
        return $this->hasMany(
            Msganswers::className(),
            ['ma_message_id' => 'ap_id']
        );
    }

    /**
     *  Связь сообщения и его соисполнителей
     */
    public function getAnswers() {
        return $this
            ->hasMany(
                User::className(),
                ['us_id' => 'ma_user_id'])
            ->via('users');
    }

    /**
     *  Установка соисполнителей
     * @param array $answers id soanswers for message
     */
    public function setAnswers($answers)
    {
        $this->answers = $answers;
    }

    /**
     *  Связь сообщения и его ответов
     */
    public function getReplies() {
        return $this
            ->hasMany(
                Answer::className(),
                ['ans_ap_id' => 'ap_id']);
    }

    /**
     *  Связь с табличкой, соединяющей сообщения и его теги
     */
    public function getMsgtags() {
        return $this->hasMany(
            Msgtags::className(),
            ['mt_msg_id' => 'ap_id']
        );
    }

    /**
     *  Связь сообщения и его тегов
     */
    public function getAlltags() {
        return $this
            ->hasMany(
                Tags::className(),
                ['tag_id' => 'mt_tag_id'])
            ->via('msgtags');
    }

    /**
     *  Установка тегов
     * @param array $tags id tags for validate
     */
    public function setAlltags($tags)
    {
        $this->alltags = $tags;
    }

    /**
     * Get user Files
     *
     * @param $bGuest boolean true - guest file, false - answer file
     * @return array of File objects
     */
    public function getUserFiles($bGuest = true) {
        $aRet = [];
        foreach($this->attachments As $ob) {
            /** @var File  $ob */
            if( $bGuest ) {
                if( $ob->file_user_id == 0 ) {
                    $aRet[] = $ob;
                }
            }
            else {
                if( $ob->file_user_id != 0 ) {
                    $aRet[] = $ob;
                }
            }
        }
        return $aRet;
    }


    /**
     * @return null|Answer
     */
    public function getLastReply() {
        $oReply = null;
        $aReply = $this->replies;
        foreach($aReply As $ob) {
            /** @var Answer $ob */
            if( $ob->ans_state == Stateflag::STATE_ANSWER_MODERATED ) {
                $oReply = $ob;
            }
        }

        return $oReply;
    }

}
