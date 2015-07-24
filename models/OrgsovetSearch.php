<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Orgsovet;

/**
 * OrgsovetSearch represents the model behind the search form about `app\models\Orgsovet`.
 */
class OrgsovetSearch extends Orgsovet
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['orgsov_id', 'orgsov_sovet_id', 'orgsov_ekis_id'], 'integer'],
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
        $query = Orgsovet::find();

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
            'orgsov_id' => $this->orgsov_id,
            'orgsov_sovet_id' => $this->orgsov_sovet_id,
            'orgsov_ekis_id' => $this->orgsov_ekis_id,
        ]);

        return $dataProvider;
    }
}
