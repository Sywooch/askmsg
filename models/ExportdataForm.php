<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\db\Query;

use app\models\Message;
use app\models\Msgflags;
use app\models\User;


/**
 * LoginForm is the model behind the login form.
 */
class ExportdataForm extends Model
{
    public $msg_createtime;
    public $msg_active;
    public $msg_pers_name;
    public $msg_pers_secname;
    public $msg_pers_lastname;
    public $msg_pers_email;
    public $msg_pers_phone;
    public $msg_pers_org;
    public $msg_pers_region;
    public $msg_pers_text;
    public $msg_comment;
    public $msg_empl_id;
    public $msg_empl_command;
    public $msg_empl_remark;
    public $msg_answer;
    public $msg_answertime;
    public $msg_flag;
    public $msg_subject;
    public $ekis_id;
    public $msg_curator_id;
    public $msg_mark;
    public $employer;
    public $asker;
    public $askid;
    public $askcontacts;
    public $tags;
    public $answers;
    public $alltags;

    public $startdate;
    public $finishdate;

    public $fieldslist;

    public $_oMsg = null;
    public $_aFlags = null;

    public $_aAllFields = [
            'msg_id',
            'msg_createtime',
            'fio',
//            'msg_pers_name',
//            'msg_pers_secname',
//            'msg_pers_lastname',
            'msg_pers_email',
            'msg_pers_phone',
            'msg_pers_org',
            'msg_pers_region',
            'msg_subject',
            'msg_pers_text',
            'msg_flag',
            'msg_answer',
            'msg_curator_id',
            'employer',
            'answers',
            'msg_comment',
            'msg_empl_command',
            'msg_empl_remark',
            'msg_answertime',
            'alltags',
            'sovetid',
            'ekis_id',
        ];



    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['msg_createtime', 'msg_answertime'], 'safe'],
//            [['answers'], 'safe'],
            [['msg_curator_id', 'msg_empl_id', 'answers',], 'in', 'range' => array_keys(User::getGroupUsers(Rolesimport::ROLE_ANSWER_DOGM, ['us_active' => User::STATUS_ACTIVE], '{{val}}')), 'allowArray' => true, ],

            [['msg_flag', ], 'in', 'range' => array_keys(Msgflags::getStateData())],

            [['fieldslist', ], 'in', 'range' => $this->_aAllFields, 'allowArray' => true, ],

            [['msg_subject', ], 'in', 'range' => array_keys(ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_SUBJECT), 'tag_id', 'tag_title')), 'allowArray' => true, ],

            [['alltags', ], 'in', 'range' => array_keys(ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_TAG), 'tag_id', 'tag_title')), 'allowArray' => true, ],

            [['ekis_id', ], 'integer'], // 'msg_active', 'msg_pers_region',

            [['msg_pers_text'], 'string', ],

            [['msg_answer', 'msg_empl_command', 'msg_empl_remark', 'msg_comment', 'msg_pers_org'], 'string'],

            [['msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', ], 'string', 'max' => 255],

            [['msg_pers_phone', ], 'match',
                'pattern' => '|^\\+7\\([\\d]{3}\\)\s+[\\d]{3}-[\\d]{2}-[\\d]{2}$|', 'message' => 'Нужно указать правильный телефон',
            ],

            [['msg_pers_email'], 'email', ],
            [['employer', 'asker', 'askid', 'askcontacts', 'tags', 'startdate', 'finishdate', ], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $a = [
            'startdate' => 'Начальная дата',
            'finishdate' => 'Конечная дата',
            'fieldslist' => 'Поля для вывода',
            'fio' => 'ФИО посетителя',
            'sovetid' => 'МРСД',
            'ekis_id' => 'Код ЕКИС',
        ];
        if( $this->_oMsg === null ) {
            $this->_oMsg = new Message();
        }
        return array_merge($this->_oMsg->attributeLabels(), $a);
    }


    /**
     * @return array
     */
    public function prepareFieldNames() {
        $a = $this->attributeLabels();
        $aret = [];
        foreach($this->_aAllFields As $v) {
            $aret[$v] = $this->getAttributeLabel($v);
        }
//        asort($aret);
        return $aret;
    }

    /**
     * @return ActiveDataProvider
     */
    public function prepareDataForSearch() {

        $query = Message::find()
            ->with('employee')
            ->with('curator')
            ->with('answers')
            ->with('alltags')
            ->with('region')
            ->with('subject')
            ->with('sovet')
            ->with('flag');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'msg_createtime'=>SORT_DESC
                ]
            ],
            'pagination' => [
                'defaultPageSize' => 100,
                'pageSize' => 100,
            ],

        ]);

        Yii::info('form attributes = ' . print_r($this->attributes, true));


//        // если указан id записи, все остальное сбрасываем
//        if( !empty($this->msg_id) ) {
//            $a = ['msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_pers_org', 'msg_flag', 'msgflags', 'msg_pers_region', 'msg_subject', 'answers', 'alltags', 'msg_createtime'];
//            foreach($a As $v) {
//                $this->$v = null;
//            }
//        }

        if( !empty($this->answers) ) {
            $ansQuery = (new Query)
                ->select('ma_message_id')
                ->from(Msganswers::tableName())
                ->where(['ma_user_id' => $this->msg_answer])
                ->distinct();
            $query->andFilterWhere(['or', ['msg_id' => $ansQuery], ['msg_empl_id' => $this->msg_empl_id]]);
        }
        else {
            $query->andFilterWhere(['or', ['msg_empl_id' => $this->msg_empl_id], ['msg_curator_id' => $this->msg_empl_id], ]);

        }

        if( !empty($this->alltags) ) {
            $tagsQuery = (new Query)
                ->select('mt_msg_id')
                ->from(Msgtags::tableName())
                ->where(['mt_tag_id' => $this->alltags])
                ->distinct();
            $query->andFilterWhere(['msg_id' => $tagsQuery]);
        }

        if( !empty($this->_flagsstring) ) {
//            $this->msgflags = Msgflags::getIdByNames(explode(',', $this->_flagsstring));
//            Yii::info('this->_flagsstring = ' . $this->_flagsstring . "\nthis->msgflags = " . implode(', ', $this->msgflags) . "\n this->msg_flag = " . ($this->msg_flag? 'use' : 'not use'));
        }

        $sdate = '';
        if( !empty($this->startdate) ) {
            if( preg_match('|^([\\d]+)\\.([\\d]+)\\.([\\d]{4})$|', $this->startdate, $a) ) {
                $sdate = date('Y-m-d', mktime(0, 0, 0, $a[2], $a[1], $a[3]));
                $query->andFilterWhere(['>=', 'msg_createtime', $sdate]);
            }
            else {
                Yii::info('Error format start date: ' . $this->startdate);
            }
        }
        if( !empty($this->finishdate) ) {
            if( preg_match('|^([\\d]+)\\.([\\d]+)\\.([\\d]{4})$|', $this->finishdate, $a) ) {
                $sdate = date('Y-m-d', mktime(0, 0, 0, $a[2], $a[1], $a[3]) + 24 * 3600);
                $query->andFilterWhere(['<', 'msg_createtime', $sdate]);
            }
            else {
                Yii::info('Error format finish date: ' . $this->finishdate);
            }
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ekis_id' => $this->ekis_id,
            'msg_subject' => $this->msg_subject,
            'msg_pers_region' => $this->msg_pers_region,
            'msg_flag' => $this->msg_flag,
        ]);

        $query->andFilterWhere(['like', 'msg_pers_name', $this->msg_pers_name])
            ->andFilterWhere(['like', 'msg_pers_secname', $this->msg_pers_secname])
            ->andFilterWhere(['like', 'msg_pers_lastname', $this->msg_pers_lastname])
            ->andFilterWhere(['like', 'msg_pers_email', $this->msg_pers_email])
            ->andFilterWhere(['like', 'msg_pers_phone', $this->msg_pers_phone])
            ->andFilterWhere(['like', 'msg_pers_org', $this->msg_pers_org]);
//            ->andFilterWhere(['like', 'msg_pers_text', $this->msg_pers_text])
//            ->andFilterWhere(['like', 'msg_comment', $this->msg_comment])
//            ->andFilterWhere(['like', 'msg_empl_command', $this->msg_empl_command])
//            ->andFilterWhere(['like', 'msg_empl_remark', $this->msg_empl_remark])
//            ->andFilterWhere(['like', 'msg_answer', $this->msg_answer])
//            ->andFilterWhere(['like', 'msg_oldcomment', $this->msg_oldcomment]);

        return $dataProvider;
    }

    /**
     * @param Message $ob
     * @param string $sField
     */
    public function getFieldValue($ob, $sField) {
//        'msg_subject',
//        'msg_flag',
//        'msg_curator_id',
//        'employer',
//        'answers',
//        'alltags',
        if( $sField == 'msg_subject' ) {
            return ($ob->subject !== null) ?
                $ob->subject->tag_title :
                '';
        }
        else if( $sField == 'msg_pers_text' ) {
            $s = $ob->msg_pers_text;
            $s = str_replace('</p>', "\n</p>", $s);
//            $s = str_replace("\n", "\r", $s);
            return strip_tags($s);
        }
        else if( $sField == 'msg_flag' ) {
            return Msgflags::getStateTitle($ob->msg_flag);
        }
        else if( $sField == 'msg_curator_id' ) {
            return ( $ob->curator !== null ) ?
                $ob->curator->getFullName() :
                '';
        }
        else if( $sField == 'employer' ) {
            return ( $ob->employee !== null ) ?
                $ob->employee->getFullName() :
                '';
        }
        else if( $sField == 'msg_pers_region' ) {
            return ( $ob->region !== null ) ?
                $ob->region->reg_name :
                '';
        }
        else if( $sField == 'answers' ) {
            return implode("\n", ArrayHelper::map($ob->answers, 'us_id', function($o) { return $o->getFullName(); }));
        }
        else if( $sField == 'fio' ) {
            return $ob->getFullName();
        }
        else if( $sField == 'sovetid' ) {
            $sSovet = $ob->sovet ? $ob->sovet->sovet_title : '';
            $a = explode(',', $sSovet);
            $sSovet = array_pop($a);
            return $sSovet;
        }
        else if( $sField == 'alltags' ) {
            return implode(",", ArrayHelper::map($ob->alltags, 'tag_id', function($o) { return $o->tag_title; }));
        }
        return $ob->$sField;
    }

    /**
     * @param string $sVal
     * @return string
     */
    public function prepareCsvValue($sVal) {
        $sMark = '';
        if( ( strpos($sVal, "\n") !== false )
         || ( strpos($sVal, ",") !== false )
         || ( strpos($sVal, ";") !== false )
         || ( strpos($sVal, '"') !== false )
         || empty($sVal) ) {
            $sMark = '"';
        }

        return $sMark
            . str_replace(
                ['"', "\r"],
                ['""' . ''],
                @iconv('UTF-8', 'CP1251', $sVal)
            )
            . $sMark;
    }
}
