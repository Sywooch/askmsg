<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Support;

/**
 * SupportSearch represents the model behind the search form about `app\models\Support`.
 */
class SupportSearch extends Support
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sup_id', 'sup_empl_id', 'sup_active'], 'integer'],
            [['sup_createtime', 'sup_message'], 'safe'],
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
        $query = Support::find();

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
            'sup_id' => $this->sup_id,
            'sup_createtime' => $this->sup_createtime,
            'sup_empl_id' => $this->sup_empl_id,
            'sup_active' => $this->sup_active,
        ]);

        $query->andFilterWhere(['like', 'sup_message', $this->sup_message]);

        return $dataProvider;
    }
}
