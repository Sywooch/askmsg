<?php

use yii\db\Schema;
use yii\db\Migration;

class m150707_094325_add_email_notif extends Migration
{
    public function up()
    {

        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';

        // табличка со сроками и действиями по этим срокам
        $this->createTable('{{%notificateact}}', [
            'ntfd_id' => Schema::TYPE_PK,
            'ntfd_message_age' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT \'Срок от создания\'',
            'ntfd_operate' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT \'Действие\'',
            'ntfd_flag' => Schema::TYPE_INTEGER . ' DEFAULT 0 COMMENT \'Дополнительные флаги\'',
        ], $tableOptionsMyISAM);

        // табличка с логами действий по обращениям
        $this->createTable('{{%notificatelog}}', [
            'ntflg_id' => Schema::TYPE_PK,
            'ntflg_msg_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT \'Обращение\'',
            'ntflg_ntfd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT \'Действие\'',
            'ntflg_notiftime' => Schema::TYPE_DATE . ' COMMENT \'Дата действия\'',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_ntflg_msg_id', '{{%notificatelog}}', 'ntflg_msg_id');
    }

    public function down()
    {
        $this->dropTable('{{%notificateact}}');
        $this->dropTable('{{%notificatelog}}');
        return true;
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
