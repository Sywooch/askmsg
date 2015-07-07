<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%notificatelog}}".
 *
 * @property integer $ntflg_id
 * @property integer $ntflg_msg_id
 * @property integer $ntflg_ntfd_id
 * @property string $ntflg_notiftime
 */
class Notificatelog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notificatelog}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ntflg_msg_id', 'ntflg_ntfd_id'], 'required'],
            [['ntflg_msg_id', 'ntflg_ntfd_id'], 'integer'],
            [['ntflg_notiftime'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ntflg_id' => 'Ntflg ID',
            'ntflg_msg_id' => 'Обращение',
            'ntflg_ntfd_id' => 'Действие',
            'ntflg_notiftime' => 'Дата действия',
        ];
    }
}
