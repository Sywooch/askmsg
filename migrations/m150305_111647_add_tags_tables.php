<?php

use yii\db\Schema;
use yii\db\Migration;
use app\models\Tags;

class m150305_111647_add_tags_tables extends Migration
{
    public function up()
    {
        $tableOptions = null;
        $tableOptionsMyISAM = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
            $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        }

        $this->createTable('{{%tags}}', [
            'tag_id' => Schema::TYPE_PK,
            'tag_active' => Schema::TYPE_SMALLINT . ' DEFAULT 0',
            'tag_type' => Schema::TYPE_INTEGER . ' NOT NULL',
            'tag_title' => Schema::TYPE_STRING,
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_tags_title', '{{%tags}}', 'tag_title');
        $this->createIndex('idx_tags_type', '{{%tags}}', 'tag_type');

        $this->createTable('{{%msgtags}}', [
            'mt_id' => Schema::TYPE_PK,
            'mt_msg_id' => Schema::TYPE_INTEGER,
            'mt_tag_id' => Schema::TYPE_INTEGER,
        ], $tableOptionsMyISAM);

        $aNames = [
            'Комплектование дошкольных групп',
            'Комплектование ',
            'Родительская плата',
            'Дополнительные образовательные услуги',
            'Государственная итоговая аттестация (ЕГЭ, ОГЭ, ГВЭ)',
            'Питание',
            'Оценка педагогов',
            'Оценка администрации',
            'Электронный журнал и дневник',
            'Учебная литература',
            'Ремонт и Строительство',
            'Летние лагеря',
            'Выпускные вечера',
            'Охрана',
            'Лицензирование',
            'Аккредитация',
            'Аттестация',
            'Апостиль',
            'Другое',
        ];

/*
Перевод из одного образовательного учреждение в другое
Жалобы на действия педагогов
Жалобы на действия администрации
Благодарности

*/
        foreach($aNames As $v) {
            $ob = new Tags();
            $ob->attributes = [
                'tag_active' => 1,
                'tag_type' => Tags::TAGTYPE_SUBJECT,
                'tag_title' => $v,
            ];
            if( !$ob->save() ) {
                echo 'Subject ' . $v . ' hasnt saved' . "\n";
            }
        }

        $this->createIndex('idx_msgtags_msgid', '{{%msgtags}}', 'mt_msg_id');
        $this->createIndex('idx_msgtags_tagid', '{{%msgtags}}', 'mt_tag_id');

    }

    public function down()
    {
//        return true;
        $this->dropIndex('idx_msgtags_msgid', '{{%msgtags}}');
        $this->dropIndex('idx_msgtags_tagid', '{{%msgtags}}');
        $this->dropTable('{{%msgtags}}');

        $this->dropIndex('idx_tags_title', '{{%tags}}');
        $this->dropIndex('idx_tags_type', '{{%tags}}');
        $this->dropTable('{{%tags}}');

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
