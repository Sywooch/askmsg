<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%tags}}".
 *
 * @property integer $tag_id
 * @property integer $tag_active
 * @property string $tag_title
 * @property integer $tag_type
 * @property integer $tag_parent_id
 */
class Tags extends \yii\db\ActiveRecord
{
    const TAGTYPE_TAG = 1;
    const TAGTYPE_SUBJECT = 2;

    public static $_aTypes = [
        self::TAGTYPE_TAG => 'Тег',
        self::TAGTYPE_SUBJECT => 'Тема',
    ];

    public static $_cache = null;

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
            [['tag_active', 'tag_type', ], 'integer'],
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
            'tag_type' => 'Тип',
            'tag_parent_id' => 'Родитель',
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
     * Получаем id тегов по их тесту
     * @param string|array $aNames текст тега или массив имен тегов
     * @param integer $nType тип для поиска
     * @return array
     */
    public static function getIdByNames($aNames, $nType = self::TAGTYPE_TAG)
    {
        if( is_string($aNames) ) {
            $aNames = [$aNames];
        }
        $aId = [];
        $aOb = ArrayHelper::map(
            self::getTagslist($nType),
            function($item) { return mb_strtolower($item->tag_title, 'UTF-8'); },
            'tag_id'
        );
        foreach($aNames As $v) {
            $st = mb_strtolower($v, 'UTF-8');
            if( !isset($aOb[$st]) ) {
                $model = new Tags();
                $model->attributes = [
                    'tag_active' => 1,
                    'tag_title' => $v,
                    'tag_type' => $nType,
                ];
                if( !$model->save() ) {
                    Yii::warning('Tags::getIdByNames() ERROR ' . print_r($model->getErrors(), true));
                    continue;
                }
                $id = $model->tag_id;
            }
            else {
                $id = $aOb[$st];
            }
            $aId[] = $id;
        }
        return $aId;
    }

    /**
     * Возвращаем записи определенного типа
     * @param int $nType тип возвращаемых значений
     * @return array
     *
     */
    public static function getTagslist($nType)
    {
        if( self::$_cache === null ) {
            self::$_cache = [];
        }
        if( isset(self::$_cache[$nType]) ) {
            return self::$_cache[$nType];
        }

        $aRet = [];
        if( isset(self::$_aTypes[$nType]) ) {
            $aRet = self::find()
                ->where(['tag_type' => $nType, 'tag_active' => 1,])
                ->orderBy('tag_title')
                ->all();
        }
        self::$_cache[$nType] = $aRet;
        return $aRet;
    }
}
