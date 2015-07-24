<?php

namespace app\models;

use Yii;
use app\models\Sovet;

/**
 * This is the model class for table "{{%orgsovet}}".
 *
 * @property integer $orgsov_id
 * @property integer $orgsov_sovet_id
 * @property string $orgsov_ekis_id
 */
class Orgsovet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%orgsovet}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['orgsov_sovet_id', 'orgsov_ekis_id'], 'required'],
            [['orgsov_sovet_id', 'orgsov_ekis_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'orgsov_id' => 'Orgsov ID',
            'orgsov_sovet_id' => 'Orgsov Sovet ID',
            'orgsov_ekis_id' => 'Orgsov Ekis ID',
        ];
    }

    public function getSovet() {
        return $this->hasOne(Sovet::className(), ['sovet_id' => 'orgsov_sovet_id']);
    }

}
