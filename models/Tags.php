<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%tags}}".
 *
 * @property integer $tag_id
 * @property integer $tag_active
 * @property string $tag_title
 * @property integer $tag_type
 */
class Tags extends \yii\db\ActiveRecord
{
    const TAGTYPE_TAG = 1;
    const TAGTYPE_SUBJECT = 2;

    public static $_aTypes = [
        self::TAGTYPE_TAG => 'Тег',
        self::TAGTYPE_SUBJECT => 'Тема',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tags}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag_title', 'tag_type'], 'required'],
            [['tag_active', 'tag_type'], 'integer'],
            [['tag_title'], 'unique', 'targetAttribute' => ['tag_title', 'tag_type']],
            [['tag_title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tag_id' => 'ID',
            'tag_active' => 'Активно',
            'tag_title' => 'Заголовок',
            'tag_type' => 'Тип'
        ];
    }

    /**
     * Возвращаем название типа
     * @return string
     *
     */
    public function getTypename()
    {
        return self::$_aTypes[$this->tag_type];
    }

    /**
     * Возвращаем записи определенного типа
     * @param int $nType тип возвращаемых значений
     * @return array
     *
     */
    public static function getTagslist($nType)
    {
        $aRet = [];
        if( isset(self::$_aTypes[$nType]) ) {
            $aRet = self::find()
                ->where(['tag_type' => $nType, 'tag_active' => 1,])
                ->all();
        }
        return $aRet;
    }
}
