<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Answer;

/**
 * AnswerSearch represents the model behind the search form about `app\models\Answer`.
 */
class AnswerSearch extends Answer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ans_id', 'ans_type', 'ans_state', 'ans_ap_id', 'ans_us_id', 'ans_mark'], 'integer'],
            [['ans_created', 'ans_text', 'ans_remark', 'ans_mark_comment'], 'safe'],
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
        $query = Answer::find();

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
            'ans_id' => $this->ans_id,
            'ans_created' => $this->ans_created,
            'ans_type' => $this->ans_type,
            'ans_state' => $this->ans_state,
            'ans_ap_id' => $this->ans_ap_id,
            'ans_us_id' => $this->ans_us_id,
            'ans_mark' => $this->ans_mark,
        ]);

        $query->andFilterWhere(['like', 'ans_text', $this->ans_text])
            ->andFilterWhere(['like', 'ans_remark', $this->ans_remark])
            ->andFilterWhere(['like', 'ans_mark_comment', $this->ans_mark_comment]);

        return $dataProvider;
    }
}
