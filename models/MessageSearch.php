<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use app\models\Message;
use app\models\Regions;
use app\models\Rolesimport;
use app\models\Tags;
use app\models\Msgtags;

/**
 * MessageSearch represents the model behind the search form about `app\models\Message`.
 */
class MessageSearch extends Message
{
    public $msgflags = [];
    public $_flagsstring = '';
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['msg_id', 'msg_active', 'msg_pers_region', 'msg_empl_id', 'msg_subject', 'ekis_id'], 'integer'],
            [['askid', '_flagsstring', ], 'string'],
            [['msg_flag', ], 'in', 'range' => array_keys(ArrayHelper::map(Msgflags::getStateData(), 'fl_id', 'fl_sname')), 'allowArray' => true],
            [['alltags'], 'in', 'range' => array_keys(ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_TAG), 'tag_id', 'tag_title')), 'allowArray' => true],
            [['msg_createtime', 'msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_pers_org', 'msg_pers_text', 'msg_comment', 'msg_empl_command', 'msg_empl_remark', 'msg_answer', 'msg_answertime', 'msg_oldcomment'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        $scenarios = Model::scenarios();
        $scenarios['index'] = [
            'msg_id',
            'msg_pers_lastname',
            'msg_createtime',
            'msg_subject',
            'ekis_id',
        ];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $a = parent::attributeLabels();
        $a['msg_pers_lastname'] = 'Автор';
        $a['msg_oldcomment'] = 'Школа';
        $a['ekis_id'] = 'Школа';
        $a['_flagsstring'] = 'Состояние';

        return $a;
/*        [
            'msg_id' => 'Номер',
            'msg_createtime' => 'Дата',
            'msg_active' => 'Видимо',
            'msg_pers_name' => 'Имя',
            'msg_pers_secname' => 'Отчество',
            'msg_pers_lastname' => 'Фамилия',
            'msg_pers_email' => 'Email',
            'msg_pers_phone' => 'Телефон',
            'msg_pers_org' => 'Учреждение',
            'msg_pers_region' => 'Район',
            'msg_pers_text' => 'Обращение',
            'msg_comment' => 'Комментарий',
            'msg_empl_id' => 'Исполнитель',
            'msg_empl_command' => 'Поручение исполнителю',
            'msg_empl_remark' => 'Замечание исполнителю',
            'msg_answer' => 'Ответ',
            'msg_answertime' => 'Дата ответа',
            'msg_oldcomment' => 'Старые теги',
            'msg_flag' => 'Состояние',
            'msg_subject' => 'Тема',
            'ekis_id' => 'Учреждение',
//            'fl_hint' => 'Описание',

            'employer' => 'Исполнитель',
            'asker' => 'Проситель',
            'answers' => 'Соисполнители',
            'askid' => 'Номер и дата',
            'askcontacts' => 'Контакты',
            'tags' => 'Теги',
            'alltags' => 'Теги',
            'file' => 'Файл',
        ];
*/
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function moderateSearch($params)
    {
        $this->load($params);

        if( empty($this->msg_id) && empty($this->msg_flag) ) {
            $a = [
//                'msg_pers_name',
//                'msg_pers_secname',
                'msg_createtime',
                'msg_pers_email',
                'msg_pers_lastname',
                'msg_empl_id',
//                'msg_pers_phone',
                'msg_pers_org',
//                'msg_flag',
//                'msg_pers_region',
                'msg_subject',
                'alltags',
            ];

            foreach($a As $v) {
                if( !empty($this->$v) ) {
                    $this->msg_flag = array_keys(ArrayHelper::map(Msgflags::getStateData(), 'fl_id', 'fl_sname'));
                    break;
                }
            }
        }

        return $this->search(null);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        if( $params !== null ) {
            $this->load($params);
        }

        $aScenario = $this->scenarios();

        $query = Message::find()
            ->with('employee')
            ->with('curator')
            ->with('answers')
            ->with('alltags')
//            ->with('attachments')
            ->with('flag');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'msg_createtime'=>SORT_DESC
                ]
            ],
            'pagination' => [
                'defaultPageSize' => 50,
                'pageSize' => 50,
            ],

        ]);


        // если указан id записи, все остальное сбрасываем
        if( !empty($this->msg_id) ) {
            $a = ['msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_pers_org', 'msg_flag', 'msgflags', 'msg_pers_region', 'msg_subject', 'answers', 'alltags', 'msg_createtime'];
            foreach($a As $v) {
                $this->$v = null;
            }
        }

        if( !empty($this->answers) ) {
            $ansQuery = (new Query)
                ->select('ma_message_id')
                ->from(Msganswers::tableName())
                ->where(['ma_user_id' => $this->answers])
                ->distinct();
            $query->andFilterWhere(['or', ['msg_id' => $ansQuery], ['msg_empl_id' => $this->msg_empl_id]]);
        }
        else {
            $query->andFilterWhere(['msg_empl_id' => $this->msg_empl_id]);
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
            $this->msgflags = Msgflags::getIdByNames(explode(',', $this->_flagsstring));
//            Yii::info('this->_flagsstring = ' . $this->_flagsstring . "\nthis->msgflags = " . implode(', ', $this->msgflags) . "\n this->msg_flag = " . ($this->msg_flag? 'use' : 'not use'));
        }

        $a = $this->getDatePeriod('msg_createtime');
        if( count($a) > 1 ) {
            $query
                ->andFilterWhere(['>=', 'msg_createtime', $a[0]])
                ->andFilterWhere(['<', 'msg_createtime', $a[1]]);
        }
        else {
            $a = $this->makeDateRange('msg_createtime');
            if (count($a) > 1) {
                $query
                    ->andFilterWhere(['>', 'msg_createtime', $a[0]])
                    ->andFilterWhere(['<', 'msg_createtime', $a[1]]);
            }
        }

        if( !empty($this->askid) ) {
            $this->prepareDateFilter($query);
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'msg_id' => $this->msg_id,
//            'msg_createtime' => $this->msg_createtime,
            'msg_subject' => $this->msg_subject,
            'msg_pers_region' => $this->msg_pers_region,
//            'msg_answertime' => $this->msg_answertime,
            'msg_flag' => $this->msg_flag ? $this->msg_flag : $this->msgflags,
        ]);

        if( !empty($this->msg_pers_lastname) ) {
            $a = explode(' ', $this->msg_pers_lastname);
            foreach($a As $v) {
                $v = trim($v);
                if( $v === '' ) {
                    continue;
                }
                $query->andFilterWhere(['or', ['like', 'msg_pers_lastname', $v], ['like', 'msg_pers_name', $v], ['like', 'msg_pers_secname', $v]] );
            }
        }

        $query->andFilterWhere(['like', 'msg_pers_name', $this->msg_pers_name])
            ->andFilterWhere(['like', 'msg_pers_secname', $this->msg_pers_secname])
//            ->andFilterWhere(['like', 'msg_pers_lastname', $this->msg_pers_lastname])
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchindex($params)
    {
        $query = Message::find()
            ->with('employee')
            ->with('answers')
            ->with('curator')
            ->with('subject')
//            ->with('alltags')
            ->with('attachments')
            ->with('flag');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'msg_createtime'=>SORT_DESC
                ]
            ],
            'pagination' => [
                'defaultPageSize' => 50,
                'pageSize' => 50,
            ],

        ]);

        $this->load($params);

/*
        if( !empty($this->alltags) ) {
            $tagsQuery = (new Query)
                ->select('mt_msg_id')
                ->from(Msgtags::tableName())
                ->where(['mt_tag_id' => $this->alltags])
                ->distinct();
            $query->andFilterWhere(['msg_id' => $tagsQuery]);
        }
*/
        $a = $this->makeDateRange('msg_createtime');
        if( count($a) > 1 ) {
            $query
                ->andFilterWhere(['>', 'msg_createtime', $a[0]])
                ->andFilterWhere(['<', 'msg_createtime', $a[1]]);
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        $query->andFilterWhere([
            'msg_id' => $this->msg_id,
            'msg_subject' => $this->msg_subject,
            'msg_flag' => $this->msgflags,
            'ekis_id' => $this->ekis_id,
        ]);

        if( !empty($this->msg_pers_lastname) ) {
            $a = explode(' ', $this->msg_pers_lastname);
            foreach($a As $v) {
                $v = trim($v);
                if( $v === '' ) {
                    continue;
                }
                $query->andFilterWhere(['or', ['like', 'msg_pers_lastname', $v], ['like', 'msg_pers_name', $v], ['like', 'msg_pers_secname', $v]] );
            }
        }

//        $query->andFilterWhere(['like', 'msg_pers_lastname', $this->msg_pers_lastname]);
//        $query->andFilterWhere(['like', 'msg_pers_org', $this->msg_pers_org]);

        return $dataProvider;
    }

    /**
     *
     * Из поля пытаемся вытащить номер сообщения или дату и добавить их к фильтрам
     *
     * @param $query
     *
     */
    public function prepareDateFilter(&$query) {
//        Yii::info('this->askid = ' . $this->askid);
        if( $this->askid != '' ) {
            if( preg_match('|^[\\d]+$|', $this->askid) ) {
                // только одни цифры - предполагаем номер
                $this->msg_id = intval($this->askid);
            }
            else {
                $a = $this->makeDateRange('askid');
                if( count($a) > 1 ) {
                    $query
                        ->andFilterWhere(['>', 'msg_createtime', $a[0]])
                        ->andFilterWhere(['<', 'msg_createtime', $a[1]]);
                }
            }
        }
    }

    /**
     *
     * Из параметров пытаемся вытащить диапазон дат: 01.01.2015-01.05.2015
     *
     * @param string $name
     *
     */
    public function getDatePeriod($name) {
        $aRet = [];
        $sData = trim($this->{$name});
        if (preg_match('|^([\\d]{2})\\.([\\d]{2})\\.([\\d]{4})\\s*-\\s*([\\d]{2})\\.([\\d]{2})\\.([\\d]{4})$|', $sData, $a)) {
//            Yii::info($sData . ' -> ' . print_r($a, true));
            $aRet = [
                date('Y-m-d H:i:s', mktime(0, 0, 0, intval($a[2]), intval($a[1]), intval($a[3]))),
                date('Y-m-d H:i:s', mktime(0, 0, 0, intval($a[5]), intval($a[4]), intval($a[6]))),
            ];
//            Yii::info('getDatePeriod('.$name.') : ' . $sData . ' -> ' . print_r($aRet, true));
        }
        else {
//            Yii::info('getDatePeriod('.$name.') : ' . $sData . ' -> no period');
        }
        return $aRet;
    }


    /**
     *
     * Из поля пытаемся вытащить номер сообщения или дату и добавить их к фильтрам
     *
     * @param $query
     *
     */
    public function makeDateRange($name) {
        $aRet = [];
        if (preg_match('|^[\\d\\.]*\\.[\\d]{4}$|', $this->{$name})) {
            // цифры с точками - предполагаем дату
            $a = explode('.', strrev($this->{$name}));
            $n = count($a);
            $y = strrev($a[0]);
            $m0 = $m1 = 0;
            $d0 = $d1 = 0;

            if ($n > 1) {
                $m0 = intval(strrev($a[1]));
                $m1 = $m0;
            }
            if ($m0 == 0) {
                $m0 = 1;
                $m1 = 12;
            }

            if ($n > 2) {
                $d0 = intval(strrev($a[2]));
                $d1 = $d0 + 1;
            }

            if ($d0 == 0) {
                $d0 = $d1 = 1;
                $m1++;
            }

            $aRet = [
                date('Y-m-d H:i:s', mktime(0, 0, 0, $m0, $d0, $y) - 1),
                date('Y-m-d H:i:s', mktime(0, 0, 0, $m1, $d1, $y)),
            ];
        }
        return $aRet;
    }

    /**
     *
     * Проверка на отсутствие данных в модели
     *
     * @return boolean
     *
     */
    public function isEmpty() {
        $b = true;
        $a = array_keys($this->attributes);
//        $aIgnore = Yii::$app->user->can(Rolesimport::ROLE_ADMIN) ? [] : ['msg_empl_id', 'answers'];
        $aIgnore = ['msg_empl_id', 'answers'];
        foreach($a As $v) {
            if( in_array($v, $aIgnore) ) {
                continue;
            }
            $b = $b && empty($this->attributes[$v]);
//            if( !empty($this->attributes[$v]) ) {
//                Yii::info('Not empty: ' . $v . ' = ' . print_r($this->attributes[$v], true));
//            }
        }
        return $b;
    }

    /**
     *
     * Получение списка наиболее употребительных
     * @param array $param
     * @return array
     *
     */
    public function instructionList($param) {
        $query = (new \yii\db\Query())
            ->select('msg_empl_command, COUNT(msg_id) As cou')
            ->from(self::tableName())
            ->where('msg_empl_command Is Not NULL')
            ->andFilterWhere(['like', 'msg_empl_command', $param['term']])
            ->groupBy('msg_empl_command')
            ->orderBy('cou DESC')
            ->offset($param['offset'])
            ->limit($param['limit']);

        $countQuery = (new \yii\db\Query())
            ->select('Count(Distinct msg_empl_command)')
            ->from(self::tableName())
            ->where('msg_empl_command Is Not NULL')
            ->andFilterWhere(['like', 'msg_empl_command', $param['term']]);

        $nStart =  1 * $param['offset'];
        $a = [];
        foreach($query->all() As $item) {
            $a[] = [
                'text' => $item['msg_empl_command'],
                'count' => $item['cou'],
                'id' => $nStart++,
            ];
        }
        $aData = [
            'total' => $countQuery->scalar(),
            'list' => $a,
        ];
        return $aData;
    }

    /**
     * Получаем массив аттрибутов для создания ссылки
     *
     * @return array
     */
    public function getSearchParams() {
        $aAttr = $this->safeAttributes();
        $aRet = [];
        $ref = new \ReflectionClass($this);
        $sName = $ref->getShortName();

        foreach($aAttr As $v) {
            if( empty($this->$v) ) {
                continue;
            }
            $aRet[$sName . '['.$v.']'] = $this->$v;
        }

        return $aRet;
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchNotificate($params, $sWhere = '')
    {
        if ($params !== null) {
            $this->load($params);
        }

        $aScenario = $this->scenarios();

        $query = Message::find()
            ->with('employee')
            ->with('curator')
            ->with('answers')
            ->with('alltags')
            ->with('flag');

        $query->andWhere($sWhere);
//        $query->andFilterWhere($sWhere);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'msg_createtime' => SORT_ASC
                ]
            ],
            'pagination' => [
                'defaultPageSize' => 50,
                'pageSize' => 50,
            ],

        ]);

        return $dataProvider;

    }


    }
