<?php

namespace app\components;

use yii;
use app\models\File;
use app\models\Appeal;
use yii\web\UploadedFile;

class AppealActions {
    /** @var Appeal $oAppeal  */
    public $oAppeal = null;

    public function __construct($appeal = null) {
        $this->oAppeal = $appeal;
    }

    public function addFilesToAppeal($files) {
        return $this->appendFiles($files, $this->oAppeal->ap_id);
    }

    /**
     *
     * Добавялем файлы к сообщению
     *
     * @param array $aFiles
     * @param int $nMessageId
     * @param int $nAnswerId
     *
     * @return integer id of new file
     */
    public function appendFiles($aFiles = [], $nMessageId = 0, $nAnswerId = 0) {
        $aId = [];
        if( empty($aFiles) ) {
            return $aId;
        }

        $nCou = $this->oAppeal->countAvalableFile();

        foreach($aFiles As $ob) {
            /** @var  UploadedFile $ob */
            if( $nCou < 1 ) {
                break;
            }

            $oFile = new File();
            $id = $oFile->addAppealFile($ob, $nMessageId, $nAnswerId);

            if( $oFile->hasErrors() ) {
                Yii::info('uploadFiles(): File error: ' . print_r($oFile->getErrors(), true));
            }
            else {
                $nCou -= 1;
                $aId[] = $id;
                Yii::info('uploadFiles(): save file ['.$nCou.'] ' . $oFile->file_orig_name . ' [' . $oFile->file_size . ']');
            }
        }

        return $aId;
    }

}