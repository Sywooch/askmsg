<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Sovet;

/**
 * SovetSearch represents the model behind the search form about `app\models\Sovet`.
 */
class SovetSearch extends Sovet
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sovet_id'], 'integer'],
            [['sovet_title'], 'safe'],
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
        $query = Sovet::find();

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
            'sovet_id' => $this->sovet_id,
        ]);

        $query->andFilterWhere(['like', 'sovet_title', $this->sovet_title]);

        return $dataProvider;
    }
}
