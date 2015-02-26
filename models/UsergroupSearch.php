<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Usergroup;

/**
 * UsergroupSearch represents the model behind the search form about `app\models\Usergroup`.
 */
class UsergroupSearch extends Usergroup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usgr_id', 'usgr_uid', 'usgr_gid'], 'integer'],
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
        $query = Usergroup::find();

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
            'usgr_id' => $this->usgr_id,
            'usgr_uid' => $this->usgr_uid,
            'usgr_gid' => $this->usgr_gid,
        ]);

        return $dataProvider;
    }
}
