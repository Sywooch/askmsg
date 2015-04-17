<?php

use yii\db\Schema;
use yii\db\Migration;
use app\models\User;
use app\models\Group;

class m150417_131447_add_admin_user extends Migration
{
    public function up()
    {

        $model = new User();
        $model->us_active = User::STATUS_ACTIVE;
        $model->scenario = 'create';
        $model->attributes = [
            'us_login' => 'autoadmin',
            'us_name' => 'Виктор',
            'us_secondname' => 'Анатольевич',
            'us_lastname' => 'Козьмин',
            'us_email' => 'KozminVA@Edu.mos.ru',
            'us_workposition' => 'Программист',
            'us_active' => 1,
            'selectedGroups' => [1, 10, 11],
        ];
        if( $model->save() ) {
            foreach($model->selectedGroups As $gid) {
                $oGroup = Group::getGroupById($gid);
                if( $oGroup !== null ) {
                    $model->link('permissions', $oGroup);
//                    Yii::info('Add group ' . $gid);
                }
            }
            echo "\n\nCreate user: see mail on " . $model->us_email . "\n\n";

//            $sHash = Yii::$app->security->generatePasswordHash('QazsE4RfvgY7');
//            $sSql = 'Update ' . $model->tableName() . ' Set us_password_hash = :hash Where us_id = :uid';
//            $nUpd = Yii::$app->db->createCommand($sSql, [':hash' => $sHash, ':uid' => $model->us_id])->execute();
//            echo "\n\nUpdated: {$nUpd} : {$sHash}\n\n";
            //$this->us_password_hash = Yii::$app->security->generatePasswordHash($password);
        }
        else {
            echo "\n\nERRORS: " . print_r($model->getErrors(), true) . "\n\n";
        }
    }

    public function down()
    {

        $model = User::findByUsername('autoadmin');
        if( $model ) {
            $model->unlinkAll('permissions', true);
            $model->delete();
        }

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
