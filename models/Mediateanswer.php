<?php

namespace app\models;

use Yii;
use app\models\Message;
use yii\behaviors\TimestampBehavior;
use app\models\Msgflags;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%mediateanswer}}".
 *
 * @property integer $ma_id
 * @property string $ma_created
 * @property string $ma_text
 * @property string $ma_remark
 * @property integer $ma_msg_id
 *
 *
 * Если обращение имеет установленное поле msg_mediate_answer_id, то проверки и замечания относятся к промежуточному ответу
 * Если msg_mediate_answer_id не установлено, но есть промежуточный ответ, то он выводится, если нет основного ответа
 */
class Mediateanswer extends \yii\db\ActiveRecord
{
    public $msg_flag = -1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mediateanswer}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors(){
        return [
                    // поставим дату ответа
                    [
                        'class' => TimestampBehavior::className(),
                        'createdAtAttribute' => 'ma_created',
                        'updatedAtAttribute' => false,
                        'value' => new Expression('NOW()'),
                    ],
                ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ma_created'], 'safe'],
            [['ma_text', 'ma_remark'], 'string'],
            [['ma_msg_id', 'msg_flag', ], 'integer'],
            [['ma_remark'], 'required',
                'when' => function($model) { return in_array($this->msg_flag, [Msgflags::MFLG_INT_REVIS_INSTR, Msgflags::MFLG_SHOW_REVIS]); },
                'whenClient' => "function (attribute, value) { return [".implode(',', [Msgflags::MFLG_INT_REVIS_INSTR, Msgflags::MFLG_SHOW_REVIS])."].indexOf(parseInt($('#".Html::getInputId($this, 'msg_flag') ."').val())) != -1 ;}"
            ]

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ma_id' => 'Номер',
            'ma_created' => 'Создан',
            'ma_text' => 'Ответ',
            'ma_remark' => 'Замечание',
            'ma_msg_id' => 'Обращение',
            'msg_flag' => 'Состояние',
        ];
    }


    /**
     * @param Message $obMessage
     */
    public static function getMediateAnswer($obMessage) {
        $ans = $obMessage->mediateanswer;
        if( $ans === null ) {
            $ans = new Mediateanswer();
        }
        return $ans;
    }

    /**
     * @param Message $obMessage
     */
    public function setMessageData(&$obMessage) {
//        $newFlag = $obMessage->msg_flag;
//        switch( $newFlag ) {
//            case Msgflags::MFLG_SHOW_NO_ANSWER:
//            case Msgflags::MFLG_SHOW_INSTR:
//            case Msgflags::MFLG_SHOW_REVIS:
//                $newFlag = Msgflags::MFLG_SHOW_NOSOGL;
//                break;
//
//            case Msgflags::MFLG_INT_INSTR:
//            case Msgflags::MFLG_INT_REVIS_INSTR:
//                $newFlag = Msgflags::MFLG_INT_NOSOGL;
//                break;
//        }

        $obMessage->msg_mediate_answer_id = $this->ma_id;
        $obMessage->msg_flag = $this->msg_flag;

        $bSave = $obMessage->save(false);

        if( !$bSave ) {
            Yii::error('Error save data for intemediate answer : ' . print_r($obMessage->getErrors(), true) . "\n" . print_r($obMessage->attributes, true));
        }

        return $bSave;
//        $obMessage->
//        Msgflags::MFLG_THANK,
//                Msgflags::MFLG_INT_FIN_INSTR,
//                Msgflags::MFLG_INT_NEWANSWER,
//                Msgflags::MFLG_INT_REVIS_INSTR,
//                Msgflags::MFLG_INT_INSTR,
//                Msgflags::MFLG_NOSHOW,
//                Msgflags::MFLG_SHOW_REVIS,
//                Msgflags::MFLG_SHOW_NO_ANSWER,
//                Msgflags::MFLG_SHOW_ANSWER,
//                Msgflags::MFLG_NEW,
//                Msgflags::MFLG_SHOW_INSTR,
//                Msgflags::MFLG_SHOW_NEWANSWER,

    }
}
