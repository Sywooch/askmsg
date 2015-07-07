<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Notificateact;

/**
 * NotificateactSearch represents the model behind the search form about `app\models\Notificateact`.
 */
class NotificateactSearch extends Notificateact
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ntfd_id', 'ntfd_message_age', 'ntfd_operate', 'ntfd_flag'], 'integer'],
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
        $query = Notificateact::find();

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
            'ntfd_id' => $this->ntfd_id,
            'ntfd_message_age' => $this->ntfd_message_age,
            'ntfd_operate' => $this->ntfd_operate,
            'ntfd_flag' => $this->ntfd_flag,
        ]);

        return $dataProvider;
    }
}
