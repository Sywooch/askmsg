<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Mediateanswer;

/**
 * MediateanswerSearch represents the model behind the search form about `app\models\Mediateanswer`.
 */
class MediateanswerSearch extends Mediateanswer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ma_id', 'ma_msg_id'], 'integer'],
            [['ma_created', 'ma_text', 'ma_remark'], 'safe'],
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
        $query = Mediateanswer::find();

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
            'ma_created' => $this->ma_created,
            'ma_msg_id' => $this->ma_msg_id,
        ]);

        $query->andFilterWhere(['like', 'ma_text', $this->ma_text])
            ->andFilterWhere(['like', 'ma_remark', $this->ma_remark]);

        return $dataProvider;
    }
}
