<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

use app\models\Appeal;
use app\models\Stateflag;

/**
 * AppealSearch represents the model behind the search form about `app\models\Appeal`.
 */
class AppealSearch extends Appeal
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ap_id', 'ap_subject', 'ap_empl_id', 'ap_curator_id', 'ekis_id', 'ap_state', 'ap_ans_state'], 'integer'],
            [['ap_created', 'ap_next_act_date', 'ap_pers_name', 'ap_pers_secname', 'ap_pers_lastname', 'ap_pers_email', 'ap_pers_phone', 'ap_pers_org', 'ap_pers_region', 'ap_pers_text', 'ap_empl_command', 'ap_comment'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     *
     * @param Query $query
     */
    public function addBaseParams(&$query)
    {
        $a = $this->makeDateRange('ap_created');
        if( count($a) > 1 ) {
            $query
                ->andFilterWhere(['>', 'ap_created', $a[0]])
                ->andFilterWhere(['<', 'ap_created', $a[1]]);
        }

        if( isset(Yii::$app->params['message.archiveperiod']) ) {
            $sdate = time() - Yii::$app->params['message.archiveperiod'] * 24 * 3600;
            $query
                ->andFilterWhere(['>', 'ap_created', date('Y-m-d', $sdate)]);
        }

        if( !empty($this->ap_pers_lastname) ) {
            $a = explode(' ', $this->ap_pers_lastname);
            foreach($a As $v) {
                $v = trim($v);
                if( $v === '' ) {
                    continue;
                }
                $query->andFilterWhere(['or', ['like', 'ap_pers_lastname', $v], ['like', 'ap_pers_name', $v], ['like', 'ap_pers_secname', $v]] );
            }
        }

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
        $query = Appeal::find()
            ->with('employee')
            ->with('answers')
            ->with('curator')
            ->with('subject')
            ->with('replies')
            ->with('attachments');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'ap_created'=>SORT_DESC
                ]
            ],
            'pagination' => [
                'defaultPageSize' => 50,
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ap_id' => $this->ap_id,
//            'ap_created' => $this->ap_created,
//            'ap_next_act_date' => $this->ap_next_act_date,
            'ap_subject' => $this->ap_subject,
//            'ap_empl_id' => $this->ap_empl_id,
//            'ap_curator_id' => $this->ap_curator_id,
            'ekis_id' => $this->ekis_id,
//            'ap_state' => $this->ap_state,
            'ap_state' => Stateflag::STATE_APPEAL_PUBLIC,
//            'ap_ans_state' => $this->ap_ans_state,
        ]);

        $this->addBaseParams($query);

//        $query->andFilterWhere(['like', 'ap_pers_name', $this->ap_pers_name])
//            ->andFilterWhere(['like', 'ap_pers_secname', $this->ap_pers_secname])
//            ->andFilterWhere(['like', 'ap_pers_lastname', $this->ap_pers_lastname])
//            ->andFilterWhere(['like', 'ap_pers_email', $this->ap_pers_email])
//            ->andFilterWhere(['like', 'ap_pers_phone', $this->ap_pers_phone])
//            ->andFilterWhere(['like', 'ap_pers_org', $this->ap_pers_org])
//            ->andFilterWhere(['like', 'ap_pers_region', $this->ap_pers_region])
//            ->andFilterWhere(['like', 'ap_pers_text', $this->ap_pers_text])
//            ->andFilterWhere(['like', 'ap_empl_command', $this->ap_empl_command])
//            ->andFilterWhere(['like', 'ap_comment', $this->ap_comment]);

        return $dataProvider;
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

        if( empty($this->ap_state) ) {
            $this->ap_state = [
                Stateflag::STATE_APPEAL_NEW,
                Stateflag::STATE_APPEAL_PUBLIC,
                Stateflag::STATE_APPEAL_PRIVATE,
            ];
        }

        if( empty($this->ap_ans_state) ) {
            $this->ap_state = [
                Stateflag::STATE_ANSWER_NONE,
                Stateflag::STATE_ANSWER_APPROVED,
                Stateflag::STATE_APPEAL_PRIVATE,
            ];
        }

        if( empty($this->ap_id) && empty($this->ap_state) && empty($this->ap_ans_state) ) {
            $a = [
                'ap_createed',
                'ap_pers_email',
                'ap_pers_lastname',
                'ap_empl_id',
//                'msg_pers_phone',
                'ap_pers_org',
                'ap_subject',
//                'alltags',
            ];

            foreach($a As $v) {
                if( !empty($this->$v) ) {
                    break;
                }
            }
        }

        $query = Appeal::find()
            ->with('employee')
            ->with('answers')
            ->with('curator')
            ->with('subject')
            ->with('replies')
            ->with('attachments');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'ap_created'=>SORT_DESC
                ]
            ],
            'pagination' => [
                'defaultPageSize' => 50,
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ap_id' => $this->ap_id,
            'ap_created' => $this->ap_created,
            'ap_subject' => $this->ap_subject,
            'ap_empl_id' => $this->ap_empl_id,
            'ap_curator_id' => $this->ap_curator_id,
            'ekis_id' => $this->ekis_id,
            'ap_state' => $this->ap_state,
            'ap_ans_state' => $this->ap_ans_state,
        ]);

        $this->addBaseParams($query);

//        $query->andFilterWhere(['like', 'ap_pers_name', $this->ap_pers_name])
//            ->andFilterWhere(['like', 'ap_pers_secname', $this->ap_pers_secname])
//            ->andFilterWhere(['like', 'ap_pers_lastname', $this->ap_pers_lastname])
//            ->andFilterWhere(['like', 'ap_pers_email', $this->ap_pers_email])
//            ->andFilterWhere(['like', 'ap_pers_phone', $this->ap_pers_phone])
//            ->andFilterWhere(['like', 'ap_pers_org', $this->ap_pers_org])
//            ->andFilterWhere(['like', 'ap_pers_region', $this->ap_pers_region])
//            ->andFilterWhere(['like', 'ap_pers_text', $this->ap_pers_text])
//            ->andFilterWhere(['like', 'ap_empl_command', $this->ap_empl_command])
//            ->andFilterWhere(['like', 'ap_comment', $this->ap_comment]);

        return $dataProvider;
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
        $aIgnore = ['ap_empl_id', 'answers'];
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


}
