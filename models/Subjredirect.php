<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

use app\models\Tags;

/**
 * This is the model class for table "{{%subjredirect}}".
 *
 * @property integer $redir_id
 * @property integer $redir_tag_id
 * @property string $redir_adress
 * @property string $redir_description
 */
class Subjredirect extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%subjredirect}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['redir_tag_id', 'redir_adress', ], 'required'], // 'redir_description',
            [['redir_tag_id'], 'integer'],
            [['redir_tag_id'], 'in', 'range' => array_keys(ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_SUBJECT), 'tag_id', 'tag_title'))],
            [['redir_adress', ], 'match',
                'pattern' => '|^https?://([-/&;=#\\.\\+\\?\\w\\[\\]]{4,})$|',
                'message' => 'Адрес должен соответствовать формату http://site.com[&page=1]',
            ],
            [['redir_adress', 'redir_description'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'redir_id' => 'Redir ID',
            'redir_tag_id' => 'Тема',
            'redir_adress' => 'Адрес',
            'redir_description' => 'Текст ссылки',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubject() {
        return $this->hasOne(Tags::className(), ['tag_id' => 'redir_tag_id']);
    }
}
