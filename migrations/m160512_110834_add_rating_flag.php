<?php

use yii\db\Schema;
use yii\db\Migration;

class m160512_110834_add_rating_flag extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tags}}', 'tag_rating_val', Schema::TYPE_INTEGER . ' Default 0 COMMENT \'Рейтинг\'');
        $this->refreshCache();

    }

    public function down()
    {
        $this->dropColumn('{{%tags}}', 'tag_rating_val');
        $this->refreshCache();
    }

    public function refreshCache()
    {
        Yii::$app->db->schema->refresh();
        Yii::$app->db->schema->getTableSchemas();
    }

    public function printStr($s)
    {
        if( DIRECTORY_SEPARATOR == '\\' ) {
            $s = mb_convert_encoding($s, 'CP-866');
        }
        echo $s;
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
