<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tags;

/**
 * TagsSearch represents the model behind the search form about `app\models\Tags`.
 */
class TagsSearch extends Tags
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag_id', 'tag_active', 'tag_type', 'tag_rating_val', ], 'integer'],
            [['tag_title'], 'safe'],
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
        $query = Tags::find();

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
            'tag_id' => $this->tag_id,
            'tag_active' => $this->tag_active,
            'tag_type' => $this->tag_type,
            'tag_rating_val' => $this->tag_rating_val,
        ]);

        $query->andFilterWhere(['like', 'tag_title', $this->tag_title]);

        return $dataProvider;
    }
}
