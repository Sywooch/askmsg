<?php

use yii\db\Schema;
use yii\db\Migration;

class m150723_131030_add_sovet_tables extends Migration
{
    public function up()
    {

        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';

        // табличка с советами директоров
        $this->createTable('{{%sovet}}', [
            'sovet_id' => Schema::TYPE_PK,
            'sovet_title' => Schema::TYPE_STRING . ' NOT NULL COMMENT \'Назвние\'',
        ], $tableOptionsMyISAM);

        // табличка с соответствием совета и id учреждения
        $this->createTable('{{%orgsovet}}', [
            'orgsov_id' => Schema::TYPE_PK,
            'orgsov_sovet_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'orgsov_ekis_id' => Schema::TYPE_BIGINT . ' NOT NULL',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_orgsov_sovet_id', '{{%orgsovet}}', 'orgsov_sovet_id');
        $this->createIndex('idx_orgsov_ekis_id', '{{%orgsovet}}', 'orgsov_ekis_id');

        $this->refreshCache();

    }

    public function down()
    {
        $this->dropTable('{{%sovet}}');
        $this->dropTable('{{%orgsovet}}');

        $this->refreshCache();

        return true;
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
