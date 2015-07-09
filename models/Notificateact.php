<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%notificateact}}".
 *
 * @property integer $ntfd_id
 * @property integer $ntfd_message_age
 * @property integer $ntfd_operate
 * @property integer $ntfd_flag
 */
class Notificateact extends \yii\db\ActiveRecord
{
    const ACTI_EMAIL_EPLOEE = 1;
    const ACTI_EMAIL_CONTROLER = 2;
    const ACTI_EMAIL_MODERATOR = 3;

    const DAY_DURATION = 86400; // 24 * 3600

    public static $_allAct = null;

    public static $_todayTime = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notificateact}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ntfd_message_age', 'ntfd_operate'], 'required'],
            [['ntfd_operate'], 'in', 'range' => array_keys($this->acts)],

            [['ntfd_message_age', 'ntfd_operate', 'ntfd_flag'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ntfd_id' => 'id',
            'ntfd_message_age' => 'Срок от создания',
            'ntfd_operate' => 'Действие',
            'ntfd_flag' => 'Дополнительные флаги',
        ];
    }

    /**
     * @return array
     */
    public function getActs() {
        return [
            self::ACTI_EMAIL_EPLOEE => 'Отправить email исполнителю',
            self::ACTI_EMAIL_CONTROLER => 'Отправить email контролеру',
            self::ACTI_EMAIL_MODERATOR => 'Отправить email модератору',
        ];
    }

    /**
     * @return array
     */
    public function getActTitle($nAct) {
        $a = $this->getActs();
        return isset($a[$nAct]) ? $a[$nAct] : '?';
    }

    public static function getAdge($sDate) {
        return intval((self::getToday() + self::DAY_DURATION - strtotime($sDate)) / self::DAY_DURATION, 10);
    }

    /**
     * @param $sDate
     * @return array
     */
    public static function getDateAct($sDate) {
        $days = self::getAdge($sDate);
        Yii::info('getDateAct('.$sDate.'): ' . date("d.m.Y H:i:s", self::getToday()) . ' - ' . date("d.m.Y H:i:s", strtotime($sDate)) . ' = ' . $days);

        if( self::$_allAct === null ) {
            self::$_allAct = [];
            $aActions = Notificateact::find()->orderBy('ntfd_message_age')->all();
            /** @var Notificateact $ob */
            foreach($aActions As $ob) {
                if( !isset(self::$_allAct[$ob->ntfd_message_age]) ) {
                    self::$_allAct[$ob->ntfd_message_age] = [];
                }
                self::$_allAct[$ob->ntfd_message_age][] = $ob->getActTitle($ob->ntfd_operate);
            }
        }

        if( isset(self::$_allAct[$days]) ) {
            return self::$_allAct[$days];
        }
        return [];
    }

    /**
     * @return int|null
     */
    public static function getToday() {
        if( self::$_todayTime === null ) {
            self::$_todayTime = mktime(0, 0, 0);
//            self::$_todayTime = mktime(0, 0, 0, 3, date("j"), date('Y')) - self::DAY_DURATION;
        }
        return self::$_todayTime;
    }
}
