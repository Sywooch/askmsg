<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Message;

/**
 * MessageSearch represents the model behind the search form about `app\models\Message`.
 */
class MessageSearch extends Message
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['msg_id', 'msg_active', 'msg_pers_region', 'msg_empl_id', 'msg_flag'], 'integer'],
            [['msg_createtime', 'msg_pers_name', 'msg_pers_secname', 'msg_pers_lastname', 'msg_pers_email', 'msg_pers_phone', 'msg_pers_org', 'msg_pers_text', 'msg_comment', 'msg_empl_command', 'msg_empl_remark', 'msg_answer', 'msg_answertime', 'msg_oldcomment'], 'safe'],
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
        $query = Message::find()->with('employee');

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
            'msg_id' => $this->msg_id,
            'msg_createtime' => $this->msg_createtime,
            'msg_active' => $this->msg_active,
            'msg_pers_region' => $this->msg_pers_region,
            'msg_empl_id' => $this->msg_empl_id,
            'msg_answertime' => $this->msg_answertime,
            'msg_flag' => $this->msg_flag,
        ]);

        $query->andFilterWhere(['like', 'msg_pers_name', $this->msg_pers_name])
            ->andFilterWhere(['like', 'msg_pers_secname', $this->msg_pers_secname])
            ->andFilterWhere(['like', 'msg_pers_lastname', $this->msg_pers_lastname])
            ->andFilterWhere(['like', 'msg_pers_email', $this->msg_pers_email])
            ->andFilterWhere(['like', 'msg_pers_phone', $this->msg_pers_phone])
            ->andFilterWhere(['like', 'msg_pers_org', $this->msg_pers_org])
            ->andFilterWhere(['like', 'msg_pers_text', $this->msg_pers_text])
            ->andFilterWhere(['like', 'msg_comment', $this->msg_comment])
            ->andFilterWhere(['like', 'msg_empl_command', $this->msg_empl_command])
            ->andFilterWhere(['like', 'msg_empl_remark', $this->msg_empl_remark])
            ->andFilterWhere(['like', 'msg_answer', $this->msg_answer])
            ->andFilterWhere(['like', 'msg_oldcomment', $this->msg_oldcomment]);

        return $dataProvider;
    }
}
