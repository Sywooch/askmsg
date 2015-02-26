<?php

use yii\db\Schema;
use yii\db\Migration;
use app\models\Group;
use app\models\User;
use app\models\Usergroup;


class m150225_140020_importusers extends Migration
{
    public function up()
    {
//        return true;
//        $sf = \Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'migration.log';
//        $stime = date('d.m.Y H:i:s');
//        file_put_contents($sf, $stime . "\t" .print_r($aGr, true), FILE_APPEND);

        $connection = \Yii::$app->db;
        $oldConnection = \Yii::$app->dbold;

        $sql = 'Select ID, ACTIVE, NAME, DESCRIPTION  From b_group';
        $aOldGroup = $oldConnection->createCommand($sql)->queryAll();
        $aGrMap = [];
        $nPrint = 3;
        $sRoleData = '';
        $sRoleArray = '';
        foreach($aOldGroup As $aGr) {
            if( $nPrint-- > 0 ) {
                \Yii::info('Migrate up to ' . Group::tableName() . ' data ' . print_r($aGr, true));
            }
            $ob = new Group();
            $ob->group_active = ($aGr['ACTIVE'] == 'Y') ? 1 : 0;
            $ob->group_name = $aGr['NAME'];
            $ob->group_description = ($aGr['DESCRIPTION'] == '') ? $aGr['NAME'] : $aGr['DESCRIPTION'];
            if( !$ob->save() ) {
                echo "Group error: " . print_r($ob->getErrors(), true);
                \Yii::info('Migrate up to ' . Group::tableName() . ' errors: ' . print_r($ob->getErrors() . 'date: ' . print_r($aGr, true), true));
                return false;
            }
            $sRoleData .= "    const ROLE_{$aGr['ID']} = {$ob->group_id}; // {$aGr['NAME']}\n";
            $sRoleArray .= "        self::ROLE_{$aGr['ID']} => '".str_replace("'", "\\'", $aGr['NAME'])."',\n";
            $aGrMap[$aGr['ID']] = $ob->group_id;
        }
        if( $sRoleData != '' ) {
            $sRoleData = "<?php\nnamespace app\models;\n\n"
                . "class Rolesimport {\n\n"
                . "{$sRoleData}\n\n"
                . "    static \$roles = [\n{$sRoleArray}    ];\n\n"
                . "    static public function getRoleName(\$id) {\n        return self:\$roles[\$id];\n    }\n\n"
                . "}\n";
            $sf = \Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'Rolesimport.php';
            $sf1 = \Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Rolesimport.php';
            file_put_contents($sf, $sRoleData);
            rename($sf, $sf1);
        }
        \Yii::info("Inserted " . count($aGrMap) . " to group table");
        echo "Inserted " . count($aGrMap) . " to group table\n";
        /*

CREATE TABLE `b_user` (
  `ID` int(18) NOT NULL AUTO_INCREMENT,
  `TIMESTAMP_X` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `PASSWORD` varchar(50) NOT NULL,
  `CHECKWORD` varchar(50) DEFAULT NULL,
  `ACTIVE` char(1) NOT NULL DEFAULT 'Y',
  `LOGIN_ATTEMPTS` int(18) DEFAULT NULL,
  `LAST_ACTIVITY_DATE` datetime DEFAULT NULL,
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8;

        */
        $sql = 'Select us.*, ug.*  From b_user us Left OUTER JOIN b_user_group ug On us.ID = ug.USER_ID';
        $aOldUsers = $oldConnection->createCommand($sql)->query();
        $nPrevUid = 0;
        $nPrint = 3;
        foreach($aOldUsers As $ad) {
            if( $nPrint-- > 0 ) {
                \Yii::info('Migrate up to ' . User::tableName() . ' data ' . print_r($ad, true));
            }
            if( $nPrevUid != $ad['ID'] ) {
                $nPrevUid = $ad['ID'];
                $oUser = new User();
                $oUser->attributes = [
                    'us_xtime' => $ad['TIMESTAMP_X'],
                    'us_login' => $ad['LOGIN'],
                    'us_password_hash' => $ad['PASSWORD'],
                    'us_chekword_hash' => $ad['CHECKWORD'],
                    'us_active' => $ad['ACTIVE'] == 'Y' ? 1 : 0,
                    'us_name' => empty($ad['NAME']) ? $ad['LOGIN'] : $ad['NAME'] ,
                    'us_secondname' => $ad['SECOND_NAME'],
                    'us_lastname' => $ad['LAST_NAME'],
                    'us_email' => $ad['EMAIL'],
                    'us_logintime' => $ad['LAST_LOGIN'],
                    'us_regtime' => empty($ad['DATE_REGISTER']) ? date('YmdHis') : $ad['DATE_REGISTER'],
                    'us_workposition' => $ad['WORK_POSITION'],
                    'us_checkwordtime' => $ad['CHECKWORD_TIME'],
                    'auth_key' => '',
                    'email_confirm_token' => '',
                    'password_reset_token' => '',
                ];
                if( !$oUser->save() ) {
                    \Yii::info("Error insert into user " . print_r($oUser->getErrors(), true) . ' ' . print_r($ad, true) );
                    echo 'Error insert into user : ' . print_r($oUser->getErrors(), true) . "\n";
                }
            }
            if( !isset($aGrMap[$ad['GROUP_ID']]) ) {
                \Yii::info('Not found group user->group : ' . $ad['GROUP_ID'] . ' : ' . print_r($oUserGr->getErrors(), true) . ' ' . print_r($ad, true) );
                echo 'Not found group user->group : ' . $ad['GROUP_ID'] . "\n";
                continue;
            }
            $oUserGr = new Usergroup();
            $oUserGr->usgr_uid = $oUser->us_id;
            $oUserGr->usgr_gid = $aGrMap[$ad['GROUP_ID']];
            if( !$oUserGr->save() ) {
                \Yii::info("Error insert into user->group " . print_r($oUserGr->getErrors(), true) . ' ' . print_r($ad, true) );
                echo 'Error insert into user->group : ' . print_r($oUserGr->getErrors(), true) . "\n";
            }
        }


    }

    public function down()
    {
//        echo "m150225_140020_importusers cannot be reverted.\n";
        $a = [
            Group::tableName(),
            User::tableName(),
            Usergroup::tableName(),
        ];
        foreach($a As $v) {
            $nDel = \Yii::$app->db->createCommand('Delete From ' . $v)->execute();
            echo "Delete From {$v} : {$nDel}\n";
            \Yii::info('Migrate down: delete '. $nDel . ' records from ' . $v);
        }

        return true;
    }
}
