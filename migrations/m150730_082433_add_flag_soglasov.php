<?php

use yii\db\Schema;
use yii\db\Migration;
use app\models\Msgflags;

class m150730_082433_add_flag_soglasov extends Migration
{
    public function up()
    {
        $sql = 'Insert Into {{%msgflags}} '
             . '(fl_id, fl_name, fl_command, fl_sort, fl_glyth, fl_glyth_color, fl_sname, fl_duration, fl_hint) Values '
             . '(:id, :name, :command, :sort, :glyth, :glyth_color, :sname, :duration, :hint)';
        $aVal = [
            ':id' => Msgflags::MFLG_SHOW_NOSOGL,
            ':name' => '[112] Поручение, ответ не согласован',
            ':command' => 'Отправить ответ на согласование',
            ':sort' => 1300,
            ':glyth' => 'comment',
            ':glyth_color' => '#ff0000',
            ':sname' => 'Поруч не согл.',
            ':duration' => 7,
            ':hint' => 'Сообщение отображается на сайте, текст ответа - нет, контролер и модераторы видят сообщение',
        ];

        Yii::$app->db->createCommand($sql, $aVal)->execute();

        $aVal = [
            ':id' => Msgflags::MFLG_INT_NOSOGL,
            ':name' => '[113] ВП, ответ не согласован',
            ':command' => 'Отправить на согласование',
            ':sort' => 1400,
            ':glyth' => 'list-alt',
            ':glyth_color' => '#ff0000',
            ':sname' => 'ВП не согл.',
            ':duration' => 7,
            ':hint' => 'Сообщение не отображается на сайте, контролер и модераторы видят сообщение',
        ];

        Yii::$app->db->createCommand($sql, $aVal)->execute();
    }

    public function down()
    {
        $sql = 'Delete From {{%msgflags}} Where fl_id = :id';
        $aVal = [ ':id' => Msgflags::MFLG_SHOW_NOSOGL, ];

        Yii::$app->db->createCommand($sql, $aVal)->execute();

        $aVal = [ ':id' => Msgflags::MFLG_INT_NOSOGL, ];
        Yii::$app->db->createCommand($sql, $aVal)->execute();

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
