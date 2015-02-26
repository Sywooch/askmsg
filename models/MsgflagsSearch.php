<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Msgflags;

/**
 * MsgflagsSearch represents the model behind the search form about `app\models\Msgflags`.
 */
class MsgflagsSearch extends Msgflags
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fl_id', 'fl_sort'], 'integer'],
            [['fl_name'], 'safe'],
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
        $query = Msgflags::find();

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
            'fl_id' => $this->fl_id,
            'fl_sort' => $this->fl_sort,
        ]);

        $query->andFilterWhere(['like', 'fl_name', $this->fl_name]);

        return $dataProvider;
    }
}
