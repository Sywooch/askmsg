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
}
