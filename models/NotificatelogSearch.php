<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Notificatelog;

/**
 * NotificatelogSearch represents the model behind the search form about `app\models\Notificatelog`.
 */
class NotificatelogSearch extends Notificatelog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ntflg_id', 'ntflg_msg_id', 'ntflg_ntfd_id'], 'integer'],
            [['ntflg_notiftime'], 'safe'],
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
        $query = Notificatelog::find();

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
            'ntflg_id' => $this->ntflg_id,
            'ntflg_msg_id' => $this->ntflg_msg_id,
            'ntflg_ntfd_id' => $this->ntflg_ntfd_id,
            'ntflg_notiftime' => $this->ntflg_notiftime,
        ]);

        return $dataProvider;
    }
}
