<?php

use yii\db\Schema;
use yii\db\Migration;

class m151228_112851_add_redirect_data extends Migration
{
    public function up()
    {
        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';

        // табличка с переходами
        $this->createTable('{{%subjredirect}}', [
            'redir_id' => Schema::TYPE_PK,
            'redir_tag_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT \'Тема\'',
            'redir_adress' => Schema::TYPE_STRING . ' NOT NULL COMMENT \'Адрес\'',
            'redir_description' => Schema::TYPE_STRING . ' Default \'\' COMMENT \'Текст ссылки\'',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_redir_tag_id', '{{%subjredirect}}', 'redir_tag_id');

        $this->refreshCache();

    }

    public function down()
    {
        $this->dropTable('{{%subjredirect}}');
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
