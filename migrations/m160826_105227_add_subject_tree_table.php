<?php

use yii\db\Schema;
use yii\db\Migration;

class m160826_105227_add_subject_tree_table extends Migration
{
    public function up()
    {
        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';

        $this->createTable('{{%subject_tree}}', [
            'subj_id' => Schema::TYPE_PK . ' COMMENT \'Номер\'',
            'subj_created' => Schema::TYPE_DATETIME . ' COMMENT \'Создан\'',

            'subj_variant' => Schema::TYPE_TEXT . ' COMMENT \'Вариант для выбора\'',
            'subj_info' => Schema::TYPE_TEXT . ' COMMENT \'Информация\'',
            'subj_final_question' => Schema::TYPE_TEXT . ' COMMENT \'Вопрос\'',
            'subj_final_person' => Schema::TYPE_TEXT . ' COMMENT \'Конечная инстанция\'',
            'subj_lft' => Schema::TYPE_INTEGER . ' COMMENT \'Left index Nested Tree\'',
            'subj_rgt' => Schema::TYPE_INTEGER . ' COMMENT \'Right index Nested Tree\'',
            'subj_level' => Schema::TYPE_INTEGER . ' COMMENT \'Tree Node Level\'',
            'subj_parent_id' => Schema::TYPE_INTEGER . ' COMMENT \'Tree Node Parent Id\'',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_subj_lft', '{{%subject_tree}}', 'subj_lft');
        $this->createIndex('idx_subj_rgt', '{{%subject_tree}}', 'subj_rgt');
        $this->createIndex('idx_subj_parent_id', '{{%subject_tree}}', 'subj_parent_id');

        $this->refreshCache();
    }

    public function down()
    {
        $this->dropTable('{{%subject_tree}}');
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
