<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%subject_tree}}".
 *
 * @property integer $subj_id
 * @property string $subj_created
 * @property string $subj_variant
 * @property string $subj_info
 * @property string $subj_final_question
 * @property string $subj_final_person
 * @property integer $subj_lft
 * @property integer $subj_rgt
 * @property integer $subj_level
 * @property integer $subj_parent_id
 */
class SubjectTree extends \yii\db\ActiveRecord
{
    public static $_cache = [];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%subject_tree}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subj_created'], 'safe'],
            [['subj_variant', 'subj_info', 'subj_final_question', 'subj_final_person'], 'string'],
            [['subj_lft', 'subj_rgt', 'subj_level', 'subj_parent_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'subj_id' => 'Номер',
            'subj_created' => 'Создан',
            'subj_variant' => 'Вариант для выбора',
            'subj_info' => 'Информация',
            'subj_final_question' => 'Вопрос',
            'subj_final_person' => 'Конечная инстанция',
            'subj_lft' => 'Left index Nested Tree',
            'subj_rgt' => 'Right index Nested Tree',
            'subj_level' => 'Tree Node Level',
            'subj_parent_id' => 'Tree Node Parent Id',
        ];
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getChild() {
        return self::getChildNodes($this->subj_id);
    }

    /**
     * @param int $idParent
     * @return mixed
     */
    public static function getChildNodes($idParent = 0) {
        $sKey = 'child_' . $idParent;
        if( !isset(self::$_cache[$sKey]) ) {
            self::$_cache[$sKey] = self::find()
                ->where(['subj_parent_id' => $idParent])
                ->all();
        }
        return self::$_cache[$sKey];
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getParents() {
        $sKey = 'parents_' . $this->subj_id;
        if( !isset(self::$_cache[$sKey]) ) {
            self::$_cache[$sKey] = self::find()
                ->where(['and', ['<', 'subj_lft', $this->subj_lft], ['>', 'subj_rgt', $this->subj_rgt]])
                ->orderBy(['subj_lft' => SORT_ASC,])
                ->all();
        }
        return self::$_cache[$sKey];
    }

}
