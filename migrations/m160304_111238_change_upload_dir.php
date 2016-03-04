<?php

use yii\db\Schema;
use yii\db\Migration;

class m160304_111238_change_upload_dir extends Migration
{
    public $oldp = '/upload/files';
    public $newp = '/upload/ufiles';

    public function up()
    {
        $sOld = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], Yii::getAlias('@app/web') . $this->oldp);
        $sNew = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], Yii::getAlias('@app/web') . $this->newp);
        echo "\n\nold = {$sOld}\n\n";
        if( is_dir($sOld) ) {
            echo "\n\nrename {$sOld} to {$sNew}\n\n";
            rename($sOld, $sNew);
        }

        if( Yii::$app->params['message.file.uploaddir'] != ('@webroot' . $this->newp) ) {
            $ch = '*';
            echo "\n\n" . str_repeat($ch, 70) . "\n"
                . $ch . str_pad('Rename param: message.file.uploaddir ', 68, STR_PAD_BOTH) . "{$ch}\n"
                . $ch . str_pad('to  ' . '@webroot' . $this->newp, 68, STR_PAD_BOTH) . "{$ch}\n"
                . str_repeat($ch, 70) . "\n\n";
        }
//        $sOld = str_replace(['/', '\''], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], Yii::getAlias(Yii::$app->params['message.file.uploaddir']));
    }

    public function down()
    {
        $sNew = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], Yii::getAlias('@app/web') . $this->oldp);
        $sOld = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], Yii::getAlias('@app/web') . $this->newp);
        echo "\n\nold = {$sOld}\n\n";
        if( is_dir($sOld) ) {
            echo "\n\nrename {$sOld} to {$sNew}\n\n";
            rename($sOld, $sNew);
        }

        if( Yii::$app->params['message.file.uploaddir'] != ('@webroot' . $this->oldp) ) {
            $ch = '*';
            echo "\n\n" . str_repeat($ch, 70) . "\n"
                . $ch . str_pad('Rename param: message.file.uploaddir ', 68, ' ', STR_PAD_BOTH) . "{$ch}\n"
                . $ch . str_pad('to  ' . '@webroot' . $this->oldp, 68, ' ', STR_PAD_BOTH) . "{$ch}\n"
                . str_repeat($ch, 70) . "\n\n";
        }
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
