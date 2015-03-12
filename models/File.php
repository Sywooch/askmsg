<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;
use yii\db\Expression;

/**
 * This is the model class for table "{{%file}}".
 *
 * @property integer $file_id
 * @property string $file_time
 * @property string $file_orig_name
 * @property integer $file_msg_id
 * @property integer $file_user_id
 * @property integer $file_size
 * @property string $file_type
 * @property string $file_name
 */
class File extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_time'], 'safe'],
            [['file_orig_name', 'file_size', 'file_name'], 'required'],
            [['file_msg_id', 'file_user_id', 'file_size'], 'integer'],
            [['file_orig_name', 'file_type', 'file_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file_id' => 'ID',
            'file_time' => 'Время',
            'file_orig_name' => 'Имя файла',
            'file_msg_id' => 'Сообщение',
            'file_user_id' => 'Пользователь',
            'file_size' => 'Размер',
            'file_type' => 'Тип',
            'file_name' => 'Внутр. имя',
        ];
    }

    /**
     *
     * Make full path to file
     *
     * @return string
     *
     */
    public function getFullpath() {
        $sDir = Yii::getAlias(Yii::$app->params['message.file.uploaddir']) . DIRECTORY_SEPARATOR . sprintf("%02x", $this->file_id % 256);
        if( !is_dir($sDir) && !mkdir($sDir) ) {
            return null;
        }
        return $sDir . DIRECTORY_SEPARATOR . $this->file_name;
    }

    /**
     *
     * Make full path to file
     *
     * @return string
     *
     */
    public function getUrl() {
        $sName = $this->getFullpath();
        return str_replace(DIRECTORY_SEPARATOR, '/', substr($sName, strlen(Yii::getAlias('@webroot'))));
    }

    /**
     * Test if upload dir axists and try to create one in not
     *
     */
    public function isUploadDirExists() {
        $sDir = Yii::getAlias(Yii::$app->params['message.file.uploaddir']);
//        Yii::info("Upload dir: {$sDir}");
        if( !is_dir($sDir) ) {
//            Yii::info("Upload dir: {$sDir} not exists");
            $a = explode('/', Yii::$app->params['message.file.uploaddir']);
            $s = '';
            while( count($a) > 0 ) {
                $s .= (($s === '') ? '' : '/') . array_shift($a);
                $sd = Yii::getAlias($s);
//                Yii::info("Upload dir: try {$s} = {$sd}");
                if( !is_dir($sd) && !mkdir($sd) ) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param $ob UploadedFile
     * @param $mid integer Message id
     */
    public function addFile($ob, $mid) {
        if( !$this->isUploadDirExists() ) {
            Yii::info("Error: Upload dir not exists");
            return;
        }
        $a = explode(".", $ob->name);
        $ext = array_pop($a);

        $this->file_time = new Expression('NOW()');
        $this->file_orig_name = $ob->name;
        $this->file_size = $ob->size;
        $this->file_type = $ob->type;
        $this->file_name = Yii::$app->security->generateRandomString().".{$ext}";
        $this->file_user_id = Yii::$app->user->isGuest ? 0 : Yii::$app->user->id;
        $this->file_msg_id = $mid;
        if( $this->save() ) {
            $ob->saveAs($this->getFullpath());
        }

    }
}
