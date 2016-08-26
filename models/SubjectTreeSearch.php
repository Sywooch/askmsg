<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SubjectTree;

/**
 * SubjectTreeSearch represents the model behind the search form about `app\models\SubjectTree`.
 */
class SubjectTreeSearch extends SubjectTree
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subj_id', 'subj_lft', 'subj_rgt', 'subj_level', 'subj_parent_id'], 'integer'],
            [['subj_created', 'subj_variant', 'subj_info', 'subj_final_question', 'subj_final_person'], 'safe'],
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
        $query = SubjectTree::find();

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
            'subj_id' => $this->subj_id,
            'subj_created' => $this->subj_created,
            'subj_lft' => $this->subj_lft,
            'subj_rgt' => $this->subj_rgt,
            'subj_level' => $this->subj_level,
            'subj_parent_id' => $this->subj_parent_id,
        ]);

        $query->andFilterWhere(['like', 'subj_variant', $this->subj_variant])
            ->andFilterWhere(['like', 'subj_info', $this->subj_info])
            ->andFilterWhere(['like', 'subj_final_question', $this->subj_final_question])
            ->andFilterWhere(['like', 'subj_final_person', $this->subj_final_person]);

        return $dataProvider;
    }
}
