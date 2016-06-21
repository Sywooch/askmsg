<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Appeal;

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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Appeal::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'ap_next_act_date' => $this->ap_next_act_date,
            'ap_subject' => $this->ap_subject,
            'ap_empl_id' => $this->ap_empl_id,
            'ap_curator_id' => $this->ap_curator_id,
            'ekis_id' => $this->ekis_id,
            'ap_state' => $this->ap_state,
            'ap_ans_state' => $this->ap_ans_state,
        ]);

        $query->andFilterWhere(['like', 'ap_pers_name', $this->ap_pers_name])
            ->andFilterWhere(['like', 'ap_pers_secname', $this->ap_pers_secname])
            ->andFilterWhere(['like', 'ap_pers_lastname', $this->ap_pers_lastname])
            ->andFilterWhere(['like', 'ap_pers_email', $this->ap_pers_email])
            ->andFilterWhere(['like', 'ap_pers_phone', $this->ap_pers_phone])
            ->andFilterWhere(['like', 'ap_pers_org', $this->ap_pers_org])
            ->andFilterWhere(['like', 'ap_pers_region', $this->ap_pers_region])
            ->andFilterWhere(['like', 'ap_pers_text', $this->ap_pers_text])
            ->andFilterWhere(['like', 'ap_empl_command', $this->ap_empl_command])
            ->andFilterWhere(['like', 'ap_comment', $this->ap_comment]);

        return $dataProvider;
    }
}
