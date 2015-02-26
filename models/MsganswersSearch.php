<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Msganswers;

/**
 * MsganswersSearch represents the model behind the search form about `app\models\Msganswers`.
 */
class MsganswersSearch extends Msganswers
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ma_id', 'ma_message_id', 'ma_user_id'], 'integer'],
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
        $query = Msganswers::find();

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
            'ma_id' => $this->ma_id,
            'ma_message_id' => $this->ma_message_id,
            'ma_user_id' => $this->ma_user_id,
        ]);

        return $dataProvider;
    }
}
