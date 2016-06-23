<?php

use yii\db\Schema;
use yii\db\Migration;

class m160623_094346_add_teporal_answer_table extends Migration
{
    public function up()
    {
        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';


        $this->createTable('{{%mediateanswer}}', [
            'ma_id' => Schema::TYPE_PK . ' COMMENT \'Номер\'',
            'ma_created' => Schema::TYPE_DATETIME . ' COMMENT \'Создан\'',

            'ma_text' => Schema::TYPE_TEXT . ' COMMENT \'Ответ\'',
            'ma_remark' => Schema::TYPE_TEXT . ' COMMENT \'Замечание\'',

            'ma_msg_id' => Schema::TYPE_INTEGER . ' COMMENT \'Обращение\'',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_ma_msg_id', '{{%mediateanswer}}', 'ma_msg_id');

        $this->addColumn(
            '{{%message}}',
            'msg_mediate_answer_id',
            Schema::TYPE_INTEGER
        );
    }

    public function down()
    {
        $this->dropColumn(
            '{{%message}}',
            'msg_mediate_answer_id'
        );
        $this->dropTable('{{%mediateanswer}}');
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
