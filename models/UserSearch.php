<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * UserSearch represents the model behind the search form about `app\models\User`.
 */
class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['us_id', 'us_active'], 'integer'],
            [['us_xtime', 'us_login', 'us_password_hash', 'us_chekword_hash', 'us_name', 'us_secondname', 'us_lastname', 'us_email', 'us_logintime', 'us_regtime', 'us_workposition', 'us_checkwordtime', 'auth_key', 'email_confirm_token', 'password_reset_token'], 'safe'],
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
        $query = User::find()->with('permissions');

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
            'us_id' => $this->us_id,
            'us_xtime' => $this->us_xtime,
            'us_active' => $this->us_active,
            'us_logintime' => $this->us_logintime,
            'us_regtime' => $this->us_regtime,
            'us_checkwordtime' => $this->us_checkwordtime,
        ]);

        $query->andFilterWhere(['like', 'us_login', $this->us_login])
            ->andFilterWhere(['like', 'us_password_hash', $this->us_password_hash])
            ->andFilterWhere(['like', 'us_chekword_hash', $this->us_chekword_hash])
            ->andFilterWhere(['like', 'us_name', $this->us_name])
            ->andFilterWhere(['like', 'us_secondname', $this->us_secondname])
            ->andFilterWhere(['like', 'us_lastname', $this->us_lastname])
            ->andFilterWhere(['like', 'us_email', $this->us_email])
            ->andFilterWhere(['like', 'us_workposition', $this->us_workposition])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'email_confirm_token', $this->email_confirm_token])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token]);

        return $dataProvider;
    }
}
