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

    const MFLG_THANK = 1;            //	[110] Благодарности                      ** видимо посетителям
    const MFLG_INT_FIN_INSTR = 2;    //	[111] Выполненные внутренние поручения
    const MFLG_INT_NEWANSWER = 3;    //	[108] Внутренние ответы
    const MFLG_INT_REVIS_INSTR = 4;  //	[109] На доработку в.п.
    const MFLG_INT_INSTR = 5;        //	[107] Внутренние поручения
    const MFLG_NOSHOW = 6;           //	[106] Неопубликованные
    const MFLG_SHOW_REVIS = 7;       //	[104] На доработку                        ** видимо посетителям
    const MFLG_SHOW_NO_ANSWER = 8;   //	[105] Опубликованные без ответов          ** видимо посетителям
    const MFLG_SHOW_ANSWER = 9;      //	[103] Опубликованные ответы               ** видимо посетителям
    const MFLG_NEW = 10;             //	[100] Новые
    const MFLG_SHOW_INSTR = 11;      //	[101] Поручения                           ** видимо посетителям
    const MFLG_SHOW_NEWANSWER = 12; //	[102] Ответы                              ** видимо посетителям

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
            self::MFLG_NEW => [self::MFLG_SHOW_NO_ANSWER, self::MFLG_SHOW_INSTR, self::MFLG_INT_INSTR, self::MFLG_NOSHOW, self::MFLG_THANK, ],
            self::MFLG_NOSHOW => [self::MFLG_NEW, ],
            self::MFLG_THANK => [self::MFLG_NEW, ],
            self::MFLG_SHOW_NO_ANSWER => [self::MFLG_SHOW_INSTR, ],
            self::MFLG_SHOW_INSTR => [self::MFLG_SHOW_NEWANSWER, ],
            self::MFLG_SHOW_NEWANSWER => [self::MFLG_SHOW_ANSWER, self::MFLG_SHOW_REVIS, ],
            self::MFLG_SHOW_REVIS => [self::MFLG_SHOW_NEWANSWER, ],
            self::MFLG_INT_INSTR => [self::MFLG_INT_FIN_INSTR, ],
            self::MFLG_INT_NEWANSWER => [self::MFLG_INT_REVIS_INSTR, self::MFLG_INT_FIN_INSTR, ],
            self::MFLG_INT_REVIS_INSTR => [self::MFLG_INT_NEWANSWER],
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
