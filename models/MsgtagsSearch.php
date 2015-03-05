<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Msgtags;

/**
 * MsgtagsSearch represents the model behind the search form about `app\models\Msgtags`.
 */
class MsgtagsSearch extends Msgtags
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mt_id', 'mt_msg_id'], 'integer'],
            [['mt_tag_id'], 'safe'],
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
        $query = Msgtags::find();

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
            'mt_id' => $this->mt_id,
            'mt_msg_id' => $this->mt_msg_id,
        ]);

        $query->andFilterWhere(['like', 'mt_tag_id', $this->mt_tag_id]);

        return $dataProvider;
    }
}
