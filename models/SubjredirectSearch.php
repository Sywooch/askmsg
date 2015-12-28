<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Subjredirect;

/**
 * SubjredirectSearch represents the model behind the search form about `app\models\Subjredirect`.
 */
class SubjredirectSearch extends Subjredirect
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['redir_id', 'redir_tag_id'], 'integer'],
            [['redir_adress', 'redir_description'], 'safe'],
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
        $query = Subjredirect::find();

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
            'redir_id' => $this->redir_id,
            'redir_tag_id' => $this->redir_tag_id,
        ]);

        $query->andFilterWhere(['like', 'redir_adress', $this->redir_adress])
            ->andFilterWhere(['like', 'redir_description', $this->redir_description]);

        return $dataProvider;
    }
}
