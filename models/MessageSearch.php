<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Message;
use app\models\Regions;

/**
 * MessageSearch represents the model behind the search form about `app\models\Message`.
 */
class MessageSearch extends Message
{
    public $msgflags = [];
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['msg_id', 'msg_active', 'msg_pers_region', 'msg_empl_id', 'msg_flag'], 'integer'],
            [['askid', ], 'string'],
            [['msg_createtime', 'msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_pers_org', 'msg_pers_text', 'msg_comment', 'msg_empl_command', 'msg_empl_remark', 'msg_answer', 'msg_answertime', 'msg_oldcomment'], 'safe'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Message::find()
            ->with('region')
            ->with('employee')
            ->with('flag');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'msg_createtime'=>SORT_DESC
                ]
            ]
        ]);

/*
//        $query = Message::find()->leftJoin(['employee' => function($query) { $query->from(['us' => User::tableName()]); }], 'us.us_id = msg_empl_id');
//            ->leftJoin(User::tableName() . ' us', 'us.us_id = msg_empl_id')
//            ->leftJoin(Regions::tableName() . ' reg', 'reg.reg_id = msg_pers_region')
         $dataProvider->sort->attributes['tags'] = [
            'asc' => ['reg.reg_name' => SORT_ASC],
            'desc' => ['reg.reg_name' => SORT_DESC],
        ];
*/

//        $this->askid = '';
//        Yii::info('scenario = ' . $this->scenario . ' isAttributeActive(askid) = ' . ($this->isAttributeActive('askid') ? 'true' : 'false'));
        $this->load($params);

        $this->prepareDateFilter($query);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        $query->andFilterWhere([
            'msg_id' => $this->msg_id,
            'msg_createtime' => $this->msg_createtime,
            'msg_active' => $this->msg_active,
            'msg_pers_region' => $this->msg_pers_region,
            'msg_empl_id' => $this->msg_empl_id,
            'msg_answertime' => $this->msg_answertime,
            'msg_flag' => $this->msg_flag ? $this->msg_flag : $this->msgflags,
        ]);

        $query->andFilterWhere(['like', 'msg_pers_name', $this->msg_pers_name])
            ->andFilterWhere(['like', 'msg_pers_secname', $this->msg_pers_secname])
            ->andFilterWhere(['like', 'msg_pers_lastname', $this->msg_pers_lastname])
            ->andFilterWhere(['like', 'msg_pers_email', $this->msg_pers_email])
            ->andFilterWhere(['like', 'msg_pers_phone', $this->msg_pers_phone])
            ->andFilterWhere(['like', 'msg_pers_org', $this->msg_pers_org])
            ->andFilterWhere(['like', 'msg_pers_text', $this->msg_pers_text])
            ->andFilterWhere(['like', 'msg_comment', $this->msg_comment])
            ->andFilterWhere(['like', 'msg_empl_command', $this->msg_empl_command])
            ->andFilterWhere(['like', 'msg_empl_remark', $this->msg_empl_remark])
            ->andFilterWhere(['like', 'msg_answer', $this->msg_answer])
            ->andFilterWhere(['like', 'msg_oldcomment', $this->msg_oldcomment]);

        return $dataProvider;
    }

    /**
     *
     * Из поля пытаемся вытащить номер или дату и добавить их к фильтрам
     *
     * @param $query
     *
     */
    public function prepareDateFilter(&$query) {
        if( $this->askid != '' ) {
            if( preg_match('|^[\\d]+$|', $this->askid) ) {
                // только одни цифры - предполагаем номер
                $this->msg_id = intval($this->askid);
            }
            elseif( preg_match('|^[\\d\\.]+\\.[\\d]{4}$|', $this->askid) ) {
                // цифры с точками - предполагаем дату
                $a = explode('.', strrev($this->askid));
                $n = count($a);
                $y = strrev($a[0]);
                $m0 = $m1 = 0;
                $d0 = $d1 = 0;

                if($n > 1) {
                    $m0 = strrev($a[1]);
                    $m1 = $m0;
                }
                else {
                    $m0 = 1;
                    $m1 = 12;
                }

                if($n > 2) {
                    $d0 = strrev($a[2]);
                    $d1 = $d0 + 1;
                }
                else {
                    $d0 = $d1 = 1;
                    $m1++;
                }

                $query
                    ->andFilterWhere(['>', 'msg_createtime', date('Y-m-d H:i:s', mktime(0, 0, 0, $m0, $d0, $y) - 1)])
                    ->andFilterWhere(['<', 'msg_createtime', date('Y-m-d H:i:s', mktime(0, 0, 0, $m1, $d1, $y))]);
            }
        }
    }
}
