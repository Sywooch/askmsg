<?php

use yii\db\Schema;
use yii\db\Migration;

class m160524_073219_create_new_message_table extends Migration
{
    public function up()
    {
        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';

        $this->createTable('{{%appeal}}', [
            'ap_id' => Schema::TYPE_PK . ' COMMENT \'Номер\'',
            'ap_created' => Schema::TYPE_DATETIME . ' COMMENT \'Создано\'',
            'ap_next_act_date' => Schema::TYPE_DATETIME . ' COMMENT \'Срок действия\'', // ненулевое значение показывает, когда должно произойти следующее действие - ответ, проверка, назначение отвествтенных и т.д., когда нет даты, то нет дальнейших действий - обращение обработано
            'ap_finished' => Schema::TYPE_DATETIME . ' COMMENT \'Завершено\'', // ненулевое значение здесь показывает, что обработка завершена

            'ap_pers_name' => Schema::TYPE_STRING . ' COMMENT \'Имя\'',
            'ap_pers_secname' => Schema::TYPE_STRING . ' COMMENT \'Отчество\'',
            'ap_pers_lastname' => Schema::TYPE_STRING . ' COMMENT \'Фамилия\'',
            'ap_pers_email' => Schema::TYPE_STRING . '(128) COMMENT \'Email\'',
            'ap_pers_phone' => Schema::TYPE_STRING . '(24) COMMENT \'Телефон\'',
            'ap_pers_org' => Schema::TYPE_STRING . ' COMMENT \'Учреждение\'',
            'ap_pers_region' => Schema::TYPE_STRING . ' COMMENT \'Округ\'',

            'ap_pers_text' => Schema::TYPE_TEXT . ' COMMENT \'Обращение\'',
            'ap_empl_command' => Schema::TYPE_TEXT . ' COMMENT \'Поручение исполнителю\'',
            'ap_comment' => Schema::TYPE_TEXT . ' COMMENT \'Комментарий\'',

            'ap_subject' => Schema::TYPE_INTEGER . ' COMMENT \'Тема\'',
            'ap_empl_id' => Schema::TYPE_INTEGER . ' COMMENT \'Исполнитель\'',
            'ap_curator_id' => Schema::TYPE_INTEGER . ' COMMENT \'Контролер\'',
            'ekis_id' => Schema::TYPE_INTEGER . ' COMMENT \'Учреждение\'',

            'ap_state' => Schema::TYPE_SMALLINT . ' Default 0 COMMENT \'Состояние\'',
            'ap_is_archive' => Schema::TYPE_SMALLINT . ' Default 0 COMMENT \'В архиве\'', // здесь будем ставить флажок, чтобы не смотреть старые сообщения в выборках
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_ap_empl_id', '{{%appeal}}', 'ap_empl_id');
        $this->createIndex('idx_ap_state', '{{%appeal}}', 'ap_state');
        $this->createIndex('idx_ap_ekis_id', '{{%appeal}}', 'ekis_id');
        $this->createIndex('idx_ap_is_archive', '{{%appeal}}', 'ap_is_archive');

        $this->createTable('{{%answer}}', [
            'ans_id' => Schema::TYPE_PK . ' COMMENT \'Номер\'',
            'ans_created' => Schema::TYPE_DATETIME . ' COMMENT \'Создан\'',

            'ans_text' => Schema::TYPE_TEXT . ' COMMENT \'Ответ\'',
            'ans_remark' => Schema::TYPE_TEXT . ' COMMENT \'Замечание\'',

            'ans_state' => Schema::TYPE_SMALLINT . ' Default 0 COMMENT \'Состояние\'',
            'ans_ap_id' => Schema::TYPE_INTEGER . ' COMMENT \'Обращение\'',
            'ans_us_id' => Schema::TYPE_INTEGER . ' COMMENT \'Ответчик\'',
            'ans_mark' => Schema::TYPE_SMALLINT . ' COMMENT \'Оценка ответа\'',
            'ans_mark_comment' => Schema::TYPE_TEXT . ' COMMENT \'Комментарий к оценке\'',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_ans_ap_id', '{{%answer}}', 'ans_ap_id');
        $this->createIndex('idx_ans_us_id', '{{%answer}}', 'ans_us_id');
        $this->createIndex('idx_ans_state', '{{%answer}}', 'ans_state');

        $this->refreshCache();
    }

    public function down()
    {
        $this->dropTable('{{%answer}}');
        $this->dropTable('{{%appeal}}');
        $this->refreshCache();
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

}
