<?php

use yii\db\Schema;
use yii\db\Migration;

use app\models\Msgflags;

class m150306_100542_add_ekisid_field extends Migration
{
    public function up()
    {
        $adesc = [
            100 => 'Сообщение не отображается на сайте, только модераторы видят сообщение',
            101 => 'Сообщение, текст поручения и ответчик отображаются на сайте, ответчик видит сообщение',
            102 => 'Сообщение отображается на сайте, текст ответа - нет, модераторы видят сообщение',
            103 => 'Сообщение и текст ответа отображаются на сайте',
            104 => 'Сообщение отображается на сайте, текст ответа - нет, ответчик видит сообщение',
            105 => 'Сообщение отображается на сайте',
            106 => 'Сообщение не отображается на сайте',
            107 => 'Сообщение не отображается на сайте, ответчик видит сообщение',
            108 => 'Сообщение не отображается на сайте, модератор видит сообщение',
            109 => 'Сообщение не отображается на сайте, ответчик видит сообщение',
            110 => 'Сообщение отображается на сайте',
            111 => 'Сообщение не отображается на сайте',
        ];
        $aOb = Msgflags::find()->where('fl_id > 0')->all();

        echo "count: " . count($aOb) . "\n";
        foreach($aOb As $ob) {
            echo "{$ob->fl_name} : ";
            if( preg_match('|^\\[([\\d]+)\\]|', $ob->fl_name, $a) ) {
                echo print_r($a, true) . " " . (isset($adesc[$a[1]]) ? "exist" : "no");
                if( isset($adesc[$a[1]]) ) {
                    $ob->fl_hint = $adesc[$a[1]];
                    if( !$ob->save() ) {
                        echo print_r($ob->getErrors(), true);
                    }
                }
            }
            echo "\n";

        }
    }

    public function down()
    {

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
