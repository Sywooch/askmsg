<?php

use yii\db\Schema;
use yii\db\Migration;
use app\models\Group;
use app\models\User;
use app\models\Usergroup;
use app\models\Message;
use app\models\Msgflags;
use app\models\Msganswers;
use app\models\Regions;
use app\models\File;

class m150225_140020_importusers extends Migration
{
    public function up()
    {
        Yii::setAlias('@webroot', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'web');
//        return true;
//        $sf = \Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'migration.log';
//        $stime = date('d.m.Y H:i:s');
//        file_put_contents($sf, $stime . "\t" .print_r($aGr, true), FILE_APPEND);
/*
16:11:37 Dumping educom_site (, , )
b_file
b_group
b_iblock_element
b_iblock_element_prop_m52
b_iblock_element_prop_s52
b_iblock_property_enum
b_user
b_user_group

Running: mysqldump.exe --defaults-file="c:\users\kozminva\appdata\local\temp\tmpmzao03.cnf"  --set-gtid-purged=OFF --user=viktor_educom --host=localhost --protocol=tcp --port=24891 --default-character-set=utf8 --single-transaction=TRUE --no-data --skip-triggers "educom_site"

b_file
b_group
b_iblock_element
b_iblock_element_prop_m52
b_iblock_element_prop_s52
b_iblock_property_enum
b_user
b_user_group

*/
        $connection = \Yii::$app->db;
        $oldConnection = \Yii::$app->dbold;

        $this->addColumn('{{%msgflags}}', 'fl_hint', Schema::TYPE_TEXT);
        Yii::$app->db->schema->refresh();

        /* ************************************************************************
         * import user groups
         *
         */
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
                . "    static public function getRoleName(\$id) {\n        return self::\$roles[\$id];\n    }\n\n"
                . "}\n";
            $sf = \Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'Rolesimport.php';
            $sf1 = \Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . '_Rolesimport.php';
            file_put_contents($sf, $sRoleData);
            rename($sf, $sf1);
        }
        \Yii::info("Inserted " . count($aGrMap) . " to group table");
        echo "Inserted " . count($aGrMap) . " to group table\n";
        unset($aOldGroup);
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

        /* ************************************************************************
         * import users
         *
         */
        $sql = 'Select us.*, ug.*  From b_user us Left OUTER JOIN b_user_group ug On us.ID = ug.USER_ID';
        $aOldUsers = $oldConnection->createCommand($sql)->query();
        $nPrevUid = 0;
        $nPrint = 3;
        $aUserMap = [];

        foreach($aOldUsers As $ad) {
            if( $nPrint-- > 0 ) {
                \Yii::info('Migrate up to ' . User::tableName() . ' data ' . print_r($ad, true));
            }
            if( $nPrevUid != $ad['ID'] ) {
                $nPrevUid = $ad['ID'];
                $oUser = new User(['scenario' => 'importdata']);
                $oUser->attributes = [
                    'us_xtime' => $ad['TIMESTAMP_X'],
                    'us_login' => $ad['LOGIN'],
                    'us_password_hash' => $ad['PASSWORD'],
                    'us_chekword_hash' => $ad['CHECKWORD'],
                    'us_active' => $ad['ACTIVE'] == 'Y' ? 1 : 0,
                    'us_name' => empty($ad['NAME']) ? $ad['LOGIN'] : $ad['NAME'] ,
                    'us_secondname' => empty($ad['SECOND_NAME']) ? '' : trim($ad['SECOND_NAME']), // $ad['LOGIN']
                    'us_lastname' => empty($ad['LAST_NAME']) ? '' : trim($ad['LAST_NAME']), // $ad['LOGIN']
                    'us_email' => $ad['EMAIL'],
                    'us_logintime' => $ad['LAST_LOGIN'],
                    'us_regtime' => empty($ad['DATE_REGISTER']) ? date('YmdHis') : $ad['DATE_REGISTER'],
                    'us_workposition' => empty($ad['WORK_POSITION']) ? '' : $ad['WORK_POSITION'], // $ad['LOGIN']
                    'us_checkwordtime' => $ad['CHECKWORD_TIME'],
                    'auth_key' => '',
                    'email_confirm_token' => '',
                    'password_reset_token' => '',
                ];
                $oUser->generateAuthKey();

                if( !$oUser->save() ) {
                    \Yii::info("Error insert into user " . print_r($oUser->getErrors(), true) . ' ' . print_r($ad, true) );
                    echo 'Error insert into user : ' . print_r($oUser->getErrors(), true) . "\n";
                    return false;
                }
                else {
                    $aUserMap[$ad['ID']] = $oUser->us_id;
                }
            }
            if( !isset($aGrMap[$ad['GROUP_ID']]) ) {
                \Yii::info('Not found group user->group ['.$oUser->us_id.'] : ' . $ad['GROUP_ID'] . ' ' . print_r($ad, true) );
                echo 'Not found group user->group ['.$oUser->us_id.'] : ' . $ad['GROUP_ID'] . "\n";
                continue;
            }
            $oUserGr = new Usergroup();
            $oUserGr->usgr_uid = $oUser->us_id;
            $oUserGr->usgr_gid = $aGrMap[$ad['GROUP_ID']];
            if( !$oUserGr->save() ) {
                \Yii::info("Error insert into user->group " . print_r($oUserGr->getErrors(), true) . ' ' . print_r($ad, true) );
                echo 'Error insert into user->group : ' . print_r($oUserGr->getErrors(), true) . "\n";
            }
            /*

            Error insert into user->group : Array
    (
        [usgr_uid] => Array
            (
                [0] => Usgr Uid cannot be blank.
            )

    )

    Error insert into user : Array
    (
        [selectedGroups] => Array
            (
                [0] => Группы cannot be blank.
            )

    )

            */
        }

        echo 'import users finished' . "\n";

        /* ************************************************************************
         * import regions
         *
         */
        $sql = 'Select ID, NAME, ACTIVE From b_iblock_element Where IBLOCK_ID = 51 Order By SORT';
//        $sql = 'Select ID, NAME, ACTIVE From b_iblock_region Order By SORT';
        $aOldReg = $oldConnection->createCommand($sql)->queryAll();
        $aGegMap = [];
        foreach($aOldReg As $ad) {
            $oReg = new Regions();
            $oReg->attributes = [
                'reg_name' => $ad['NAME'],
                'reg_active' => $ad['ACTIVE'] == 'Y' ? 1 : 0,
            ];
            if( !$oReg->save() ) {
                \Yii::info("Error insert into regions " . print_r($oReg->getErrors(), true) . ' ' . print_r($ad, true) );
                echo 'Error insert into regions : ' . print_r($oReg->getErrors(), true) . "\n";
            }
            else {
                $aGegMap[$ad['ID']] = $oReg->reg_id;
            }
        }
        unset($aOldReg);
        echo 'import regions finished' . "\n";

        /* ************************************************************************
         * import flags
         *
         */
        $aDop = [
            '[100]' => ['comm' => 'Перенести в новые', 'glyth' => 'envelope', 'color' => '#ff0000', 'sname' =>'Новое'],
            '[101]' => ['comm' => 'Опубликовать и отправить', 'glyth' => 'comment', 'color' => '#ff4500', 'sname' =>'Поруч.'], // Отправить поручение
            '[102]' => ['comm' => 'Отправить ответ', 'glyth' => 'comment', 'color' => '#ff1493', 'sname' =>'Ответ Поруч.'],
            '[103]' => ['comm' => 'Принять и опубликовать', 'glyth' => 'comment', 'color' => '#2e8b57', 'sname' =>'Выполн. Поруч.'], // Опубликовать с ответом
            '[104]' => ['comm' => 'Отправить на доработку', 'glyth' => 'comment', 'color' => '#ff0000', 'sname' =>'Дораб. Поруч'], // Отправить на доработку
            '[105]' => ['comm' => 'Опубликовать на сайте', 'glyth' => 'comment', 'color' => '#ff0000', 'sname' =>'Опубл.'], // Опубликовать без ответа
            '[106]' => ['comm' => 'В архив', 'glyth' => 'trash', 'color' => '#cccccc', 'sname' =>'Архив'], // Отправить в архив
            '[107]' => ['comm' => 'Отправить ответчику', 'glyth' => 'list-alt', 'color' => '#ff4500', 'sname' =>'ВП'], // Отправить внутреннее поручение
            '[108]' => ['comm' => 'Отправить ответ', 'glyth' => 'list-alt', 'color' => '#ff1493', 'sname' =>'Ответ ВП'], // Отправить внутренний ответ
            '[109]' => ['comm' => 'Отправить на доработку', 'glyth' => 'list-alt', 'color' => '#ff0000', 'sname' =>'Дораб. ВП'], // Отправить на дработку внутреннее поручение
            '[110]' => ['comm' => 'Перенести в благодарности', 'glyth' => 'heart', 'color' => '#99FF99', 'sname' =>'Благ.'],
            '[111]' => ['comm' => 'Принять', 'glyth' => 'list-alt', 'color' => '#2e8b57', 'sname' =>'Выпол. ВП'], // Отчитаться о внутреннем поручении
        ];
        $sql = 'Select ID, VALUE, SORT From b_iblock_property_enum Where PROPERTY_ID = 201';
        $aOldFlags = $oldConnection->createCommand($sql)->queryAll();
        $aFlagsMap = [];
        foreach($aOldFlags As $ad) {
            $oFlag = new Msgflags();
            $av = [
                'fl_name' => $ad['VALUE'],
                'fl_sort' => $ad['SORT'],
            ];
            foreach($aDop As $k=>$v) {
                if( strncmp($k, trim($ad['VALUE']), mb_strlen($k, 'UTF-8')) == 0 ) {
                    echo "{$k} : {$ad['VALUE']}\n";
                    $av = array_merge(
                        $av,
                        [
                            'fl_command' => $v['comm'],
                            'fl_glyth' => $v['glyth'],
                            'fl_glyth_color' => $v['color'],
                            'fl_sname' => $v['sname'],
                        ]
                    );
                    break;
                }
            }
            $oFlag->attributes = $av;
            if( !$oFlag->save() ) {
                \Yii::info("Error insert into flags " . print_r($oFlag->getErrors(), true) . ' ' . print_r($ad, true) );
                echo 'Error insert into flags : ' . print_r($oFlag->getErrors(), true) . "\n";
            }
            else {
                $aFlagsMap[$ad['ID']] = $oFlag->fl_id;
            }
            echo "flag: {$ad['ID']} -> {$oFlag->fl_id} : {$ad['VALUE']}\n";
        }
        unset($aOldFlags);
        echo 'import flags finished' . "\n";

        /* ************************************************************************
         * import messages
         *
         */
        $sql = 'Select m.ID As MSGID, m.*, p.*, a.*, a.VALUE As dopuser '
              . 'From b_iblock_element_prop_s52 p, b_iblock_element m '
              . 'Left Outer Join b_iblock_element_prop_m52 a On a.IBLOCK_ELEMENT_ID = m.ID '
              . 'Where m.IBLOCK_ID = 52 And p.IBLOCK_ELEMENT_ID = m.ID And LENGTH(m.PREVIEW_TEXT) > 0'; //  And m.ID > 82510 Order By m.ID Limit 20

        $aMsg = $oldConnection->createCommand($sql)->query();
        $nCount = $aMsg->count();
        echo 'message get ' . $nCount . " records\n";
        $nPrevMsg = 0;
        $nPrint = 3;
        $n = 0;
        $nNewUser = 0;
        $nUserPrint = 5;
        foreach($aMsg As $ad) {
/*
            if( empty($ad['PREVIEW_TEXT']) ) {
                continue;
            }
 */
            $nUserPrint--;
            if( $nPrint-- > 0 ) {
                \Yii::info('Migrate up to ' . Message::tableName() . ' data ' . print_r($ad, true));
            }
            if( $n++ % 500 == 0 ) {
                echo date('H:i:s') . ' message read ' . $n . '/' . $nCount . " records\n";
            }
            if( $nPrevMsg != $ad['MSGID'] ) {
                if( $nNewUser++ % 500 == 0 ) {
                    echo date('H:i:s') . ' new message ' . $nNewUser . '/' . $n . " records\n";
                }
                $nPrevMsg = $ad['MSGID'];
                $oMsg = new Message(['scenario' => 'importdata']);

                if( isset($aGegMap[$ad['PROPERTY_200']]) ) {
                    $ad['PROPERTY_200'] = $aGegMap[$ad['PROPERTY_200']];
                }
                else {
                    echo 'Not found region : ' . $ad['PROPERTY_200'] . " [{$ad['MSGID']}]\n";
                }
                $oMsg->attributes = [
                    'msg_id' => $ad['MSGID'],
                    'msg_createtime' => $ad['DATE_CREATE'],
                    'msg_active' => $ad['ACTIVE'] == 'Y' ? 1 : 0,
                    'msg_pers_text' => strip_tags(htmlspecialchars_decode($ad['PREVIEW_TEXT'])),
                    'msg_answer' => $ad['DETAIL_TEXT'],
                    'msg_oldcomment' => $ad['TAGS'],
                    'msg_pers_lastname' => $ad['PROPERTY_194'],
                    'msg_pers_name' => $ad['PROPERTY_195'],
                    'msg_pers_secname' => ($ad['PROPERTY_196'] === null) ? '' : $ad['PROPERTY_196'],
                    'msg_pers_email' => $ad['PROPERTY_197'],
                    'msg_pers_phone' => $ad['PROPERTY_198'],
                    'msg_pers_org' => ($ad['PROPERTY_199'] === null) ? '' : trim($ad['PROPERTY_199']), // mb_substr($ad['PROPERTY_199'], 0, 255, 'UTF-8'),
                    'msg_pers_region' => $ad['PROPERTY_200'],
                    'msg_flag' => isset($aFlagsMap[$ad['PROPERTY_201']]) ? $aFlagsMap[$ad['PROPERTY_201']] : 0,
                    'msg_comment' => $ad['PROPERTY_202'],
                    'msg_empl_id' => empty($ad['PROPERTY_207']) ? $ad['PROPERTY_207'] : $aUserMap[$ad['PROPERTY_207']],
                    'msg_empl_command' => $ad['PROPERTY_215'], // empty($ad['PROPERTY_215']) ? 'Ответить' :
                    'msg_empl_remark' => $ad['PROPERTY_216'],
                ];
                if( !empty($oMsg->msg_empl_id) ) {
                    if( $oMsg->msg_flag == Msgflags::MFLG_NEW ) { // при назначеном исполнителе и новом обращении - делаем внутреннее поручение
                        $oMsg->msg_flag = Msgflags::MFLG_INT_INSTR;
                    }
//                    elseif() {}
                }

                $sOutUser = $ad['PROPERTY_194'] . ' -> ' . $oMsg->msg_pers_lastname;

                if( !$oMsg->save() ) {
                    \Yii::info("Error insert into message " . print_r($oMsg->getErrors(), true) . ' ' . print_r($ad, true) );
                    echo 'Error insert into message : ' . print_r($oMsg->getErrors(), true) . "\n";
                }
                else {
                    if( !empty($ad['dopuser']) ) {
                        $oDop = new Msganswers();
                        $oDop->ma_message_id = $oMsg->msg_id;
                        $oDop->ma_user_id = $aUserMap[$ad['dopuser']];
                        if( !$oDop->save() ) {
                            \Yii::info("Error insert into dopanswer " . print_r($oDop->getErrors(), true) . ' ' . print_r($ad, true) );
                            echo 'Error insert into dopanswer : ' . print_r($oDop->getErrors(), true) . "\n";
                        }
                    }
                    if( !empty($ad['PROPERTY_206']) ) {
                        $aFile = $this->getFileData($ad['PROPERTY_206']);
                        if( $aFile !== null ) {
                            $this->addFileToMsg($oMsg, $aFile);
                        }
                    }
                }
                
                if( $nUserPrint > 0 ) {
                    echo $sOutUser . ' -> ' . $oMsg->msg_pers_lastname . "\n";
                }

            }
            else {
                if( !empty($ad['dopuser']) ) {
                    $oDop = new Msganswers();
                    $oDop->ma_message_id = $oMsg->msg_id;
                    $oDop->ma_user_id = $aUserMap[$ad['dopuser']];
                    if( !$oDop->save() ) {
                        \Yii::info("Error insert into dopanswer " . print_r($oDop->getErrors(), true) . ' ' . print_r($ad, true) );
                        echo 'Error insert into dopanswer : ' . print_r($oDop->getErrors(), true) . "\n";
                    }
                }
            }
        }

    }

    public function down()
    {
//        return true;
        Yii::setAlias('@webroot', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'web');
//        echo "m150225_140020_importusers cannot be reverted.\n";
        $a = [
            Msganswers::tableName(),
            Usergroup::tableName(),
            Message::tableName(),
            Msgflags::tableName(),
            Group::tableName(),
            User::tableName(),
        ];
        foreach($a As $v) {
            $nDel = \Yii::$app->db->createCommand('Delete From ' . $v)->execute();
            echo "Delete From {$v} : {$nDel}\n";
            \Yii::info('Migrate down: delete '. $nDel . ' records from ' . $v);
        }
        $this->dropColumn('{{%msgflags}}', 'fl_hint');

        $this->delUploadFiles();

        return true;
    }

    /**
     * @param integer $fileId id записи в таблице файлов
     * @return array
     *
     */
    public function getFileData($fileId) {
        /* ************************************************************************
         * import messages
         *
         */
        $oldConnection = \Yii::$app->dbold;
        $sql = 'Select * '
            . 'From b_file '
            . 'Where ID = ' . $fileId;

        $aFile = $oldConnection->createCommand($sql)->queryOne();
        if( $aFile === false ) {
            return null;
        }
        if( !isset(Yii::$app->params['import.file.dir']) || !is_dir(Yii::$app->params['import.file.dir']) ) {
            echo "Not exists src file dir";
            return null;
        }

        $sFile = Yii::$app->params['import.file.dir'] . str_replace('/', DIRECTORY_SEPARATOR, $aFile['SUBDIR']) . DIRECTORY_SEPARATOR . $aFile['FILE_NAME'];
        if( !file_exists($sFile) ) {
            echo "Not exists file {$sFile} [{$fileId}]\n";
            return null;
        }
//        else {
//            echo "File {$sFile} [{$fileId}]\n";
//        }

        $aFile['path'] = $sFile;
        return $aFile;
    }

    /**
     * @param Message $oMsg
     * @param array $aFile
     */
    public function addFileToMsg($oMsg, $aFile) {
//        echo 'addFileToMsg: ' . print_r($aFile, true) . "\n";
        $oFile = new File();
//        $a = explode(".", $aFile['ORIGINAL_NAME']);
//        $ext = array_pop($a);

        $oFile->attributes = [
            'file_time' => $aFile['TIMESTAMP_X'],
            'file_orig_name' => $aFile['ORIGINAL_NAME'],
            'file_size' => $aFile['FILE_SIZE'],
            'file_type' => $aFile['CONTENT_TYPE'],
            'file_name' => $aFile['FILE_NAME'],
            'file_user_id' => $oMsg->msg_empl_id,
            'file_msg_id' => $oMsg->msg_id,
        ];

        if( $oFile->save() ) {
//            echo "Copy {$aFile['path']} -> " . $oFile->getFullpath() . "\n";
            copy($aFile['path'], $oFile->getFullpath());
        }
        else {
//            echo "Not copy {$aFile['path']} -> " . $oFile->getFullpath() . "\n";
            echo print_r($oFile->getErrors(), true);
        }

    }

    /**
     * @param string $sDir
     */
    public function delUploadFiles($sDir = '') {
        if( $sDir === '' ) {
            $sDir = rtrim(str_replace('/', DIRECTORY_SEPARATOR, Yii::getAlias(Yii::$app->params['message.file.uploaddir'])), DIRECTORY_SEPARATOR);
        }
        $a = glob($sDir . DIRECTORY_SEPARATOR . '*');
        foreach( $a As $v ) {
            $sName = basename($v);
            if( ($sName == '.') || ($sName == '..') ) {
                continue;
            }
            if( is_dir($v) ) {
                $this->delUploadFiles($v);
            }
            else {
                unlink($v);
            }
        }
    }
}
