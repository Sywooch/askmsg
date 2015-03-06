<?php

use yii\db\Schema;
use yii\db\Migration;

class m150306_100542_add_ekisid_field extends Migration
{
    public function up()
    {
//        $this->addColumn('{{%message}}', 'ekis_id', Schema::TYPE_BIGINT);

    }

    public function down()
    {
//        $this->dropColumn('{{%message}}', 'ekis_id');

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
