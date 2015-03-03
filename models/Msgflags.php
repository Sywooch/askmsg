<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%msgflags}}".
 *
 * @property integer $fl_id
 * @property string $fl_name
 * @property integer $fl_sort
 * @property string $fl_command
 */
class Msgflags extends \yii\db\ActiveRecord
{

    const MSGFLAG_THANK = 1;            //	[110] Благодарности                      ** видимо посетителям
    const MSGFLAG_INT_INSTR_FIN = 2;    //	[111] Выполненные внутренние поручения
    const MSGFLAG_INT_NEWANSWER = 3;    //	[108] Внутренние ответы
    const MSGFLAG_INT_INSTR_REVIS = 4;  //	[109] На доработку в.п.
    const MSGFLAG_INT_INSTR = 5;        //	[107] Внутренние поручения
    const MSGFLAG_NOSHOW = 6;           //	[106] Неопубликованные
    const MSGFLAG_SHOW_REVIS = 7;       //	[104] На доработку                        ** видимо посетителям
    const MSGFLAG_SHOW_NO_ANSWER = 8;   //	[105] Опубликованные без ответов          ** видимо посетителям
    const MSGFLAG_SHOW_ANSWER = 9;      //	[103] Опубликованные ответы               ** видимо посетителям
    const MSGFLAG_NEW = 10;             //	[100] Новые
    const MSGFLAG_SHOW_INSTR = 11;      //	[101] Поручения                           ** видимо посетителям
    const MSGFLAG_SHOW_NEWWANSWER = 12; //	[102] Ответы                              ** видимо посетителям

    public static $_aNames = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%msgflags}}';
    }

    /**
     * Выдача данных состояний
     *
     * @param int $nState состояние
     * @return array данные
     *
     */

    public static function getStateData($nState = null)
    {
        if( self::$_aNames === null ) {
            $aData = self::find()->asArray()->all();
            Yii::info("getStateData(): aData = " . print_r($aData, true));
            self::$_aNames = [];
            foreach($aData As $a) {
                $a['title'] = trim(preg_replace('|^\\[[^\\]]+\\]\\s+|', '', $a['fl_name']));
                self::$_aNames[$a['fl_id']] = $a;
            }
            Yii::info("getStateData(): self::_aNames = " . print_r(self::$_aNames, true));
        }
        return ($nState === null) ?
                    self::$_aNames :
                    ( isset(self::$_aNames[$nState]) ?
                        self::$_aNames[$nState] :
                        []
                    );
    }

    /**
     * Выдача заголовка состояния
     *
     * @param int $nState состояние
     * @return string название
     *
     */

    public static function getStateTitle($nState = null, $name = 'title')
    {
        $aData = self::getStateData($nState);
        Yii::info("getStateTitle(): aData = " . print_r($aData, true));
        return (is_array($aData) && isset($aData[$name])) ?
                    $aData[$name] :
                    '';
    }

    /**
     * Выдача возможных переходов из текущего состояния
     *
     * @param int $nState состояние, из которого хотим получить возможные варианты
     * @return array вариантьы переходов
     *
     */
    public static function getStateTrans($nState = 0)
    {
        $aTrans = [
            self::MSGFLAG_NEW => [self::MSGFLAG_SHOW_NO_ANSWER, self::MSGFLAG_SHOW_INSTR, self::MSGFLAG_INT_INSTR, self::MSGFLAG_NOSHOW, self::MSGFLAG_THANK, ],
            self::MSGFLAG_NOSHOW => [self::MSGFLAG_NEW, ],
            self::MSGFLAG_THANK => [self::MSGFLAG_NEW, ],
            self::MSGFLAG_SHOW_NO_ANSWER => [self::MSGFLAG_SHOW_INSTR, ],
            self::MSGFLAG_SHOW_INSTR => [self::MSGFLAG_SHOW_NEWWANSWER, ],
            self::MSGFLAG_SHOW_NEWWANSWER => [self::MSGFLAG_SHOW_ANSWER, self::MSGFLAG_SHOW_REVIS, ],
            self::MSGFLAG_SHOW_REVIS => [self::MSGFLAG_SHOW_NEWWANSWER, ],
            self::MSGFLAG_INT_INSTR => [self::MSGFLAG_INT_NEWANSWER, ],
            self::MSGFLAG_INT_NEWANSWER => [self::MSGFLAG_INT_INSTR_REVIS, self::MSGFLAG_INT_INSTR_FIN, ],
            self::MSGFLAG_INT_INSTR_REVIS => [self::MSGFLAG_INT_NEWANSWER],
        ];
        return isset($aTrans[$nState]) ? array_merge($aTrans[$nState]) : [];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fl_name'], 'required'],
            [['fl_sort'], 'integer'],
            [['fl_name', 'fl_command'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fl_id' => 'ID',
            'fl_name' => 'Имя',
            'fl_sort' => 'Сортировка',
            'fl_command' => 'Операция',
        ];
    }
}
