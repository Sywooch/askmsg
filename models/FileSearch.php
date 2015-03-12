<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\File;

/**
 * FileSearch represents the model behind the search form about `app\models\File`.
 */
class FileSearch extends File
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_id', 'file_msg_id', 'file_user_id', 'file_size'], 'integer'],
            [['file_time', 'file_orig_name', 'file_type', 'file_name'], 'safe'],
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
        $query = File::find();

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
            'file_id' => $this->file_id,
            'file_time' => $this->file_time,
            'file_msg_id' => $this->file_msg_id,
            'file_user_id' => $this->file_user_id,
            'file_size' => $this->file_size,
        ]);

        $query->andFilterWhere(['like', 'file_orig_name', $this->file_orig_name])
            ->andFilterWhere(['like', 'file_type', $this->file_type])
            ->andFilterWhere(['like', 'file_name', $this->file_name]);

        return $dataProvider;
    }
}
