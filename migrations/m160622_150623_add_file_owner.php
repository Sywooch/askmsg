<?php

use yii\db\Schema;
use yii\db\Migration;

class m160622_150623_add_file_owner extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%file}}',
            'file_table_name',
            Schema::TYPE_STRING
        );

        $sSql = 'Update {{%file}} Set file_table_name = \'appeal\' Where file_id > 0';
        $this->db->createCommand($sSql)->execute();

        $this->createIndex('idx_file_table_name', '{{%file}}', 'file_table_name');
        $this->refreshCache();

    }

    public function down()
    {
        $this->dropColumn(
            '{{%file}}',
            'file_table_name'
        );
        $this->refreshCache();
    }

    public function refreshCache()
    {
        Yii::$app->db->schema->refresh();
        Yii::$app->db->schema->getTableSchemas();
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
