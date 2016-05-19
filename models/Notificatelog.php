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
            'ntflg_id' => 'id',
            'ntflg_msg_id' => 'Обращение',
            'ntflg_ntfd_id' => 'Действие',
            'ntflg_notiftime' => 'Дата действия',
        ];
    }

    /**
     * @param $msgId
     * @throws \yii\db\Exception
     */
    public static function addNotify($msgId) {
        $sSql = 'Update ' . self::tableName() . ' Set ntflg_msg_id = ' . $msgId . ', ntflg_notiftime = NOW() Where ntflg_msg_id = 0 Limit 1';
        $command = Yii::$app->db->createCommand($sSql);
        if( $command->execute() == 0 ) {
            $sSql = 'Insert Into ' . self::tableName() . ' (ntflg_msg_id, ntflg_notiftime) values (' . $msgId . ',  NOW())';
            Yii::$app->db->createCommand($sSql)->execute();
        }
    }

    /**
     *
     * @throws \yii\db\Exception
     * @return integer Number of cleared records
     * 
     */
    public static function clearNotify() {
        $sSql = 'Update ' . self::tableName() . ' Set ntflg_msg_id = 0, ntflg_notiftime = NULL Where ntflg_notiftime <> DATE(NOW()) And ntflg_msg_id Is Not Null';
        return Yii::$app->db->createCommand($sSql)->execute();
    }

}
