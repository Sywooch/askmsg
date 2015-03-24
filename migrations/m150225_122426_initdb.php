<?php

use yii\db\Schema;
use yii\db\Migration;

class m150225_122426_initdb extends Migration
{
    public function up()
    {
        $tableOptions = null;
        $tableOptionsMyISAM = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
            $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        }

        // create user table
        $this->createTable('{{%user}}', [
            'us_id' => Schema::TYPE_PK,
            'us_xtime' => Schema::TYPE_DATETIME, // . ' NOT NULL',
            'us_login' => Schema::TYPE_STRING . ' NOT NULL',
            'us_password_hash' => Schema::TYPE_STRING . ' NOT NULL',
            'us_chekword_hash' => Schema::TYPE_STRING, // . ' NOT NULL',
            'us_active' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'us_name' => Schema::TYPE_STRING . ' NOT NULL',
            'us_secondname' => Schema::TYPE_STRING, // . ' NOT NULL',
            'us_lastname' => Schema::TYPE_STRING, // . ' NOT NULL',
            'us_email' => Schema::TYPE_STRING . ' NOT NULL',
            'us_logintime' => Schema::TYPE_DATETIME, // . ' NOT NULL',
            'us_regtime' => Schema::TYPE_DATETIME . ' NOT NULL',
            'us_workposition' => Schema::TYPE_STRING, // . ' NOT NULL',
            'us_checkwordtime' => Schema::TYPE_DATETIME, // . ' NOT NULL',

// дальше наши дополнительные поля
            'auth_key' => Schema::TYPE_STRING . '(32) NULL DEFAULT NULL',
            'email_confirm_token' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'password_reset_token' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
        ], $tableOptions);
 
        $this->createIndex('idx_user_username', '{{%user}}', 'us_login');
        $this->createIndex('idx_user_email', '{{%user}}', 'us_email');
        $this->createIndex('idx_user_status', '{{%user}}', 'us_active');
        /*
CREATE TABLE `b_user` (
  `ID` int(18) NOT NULL AUTO_INCREMENT,
  `TIMESTAMP_X` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, --------> us_xtime
  `LOGIN` varchar(50) NOT NULL,  ------------------> us_login
  `PASSWORD` varchar(50) NOT NULL,  ---------------> us_password_hash
  `CHECKWORD` varchar(50) DEFAULT NULL,  ----------> us_chekword_hash
  `ACTIVE` char(1) NOT NULL DEFAULT 'Y',  ---------> us_active Y = 1, N = 0
  `NAME` varchar(50) DEFAULT NULL,    -------------> us_name
  `LAST_NAME` varchar(50) DEFAULT NULL,  ----------> us_lastname
  `EMAIL` varchar(255) NOT NULL,       ------------> us_email
  `LAST_LOGIN` datetime DEFAULT NULL,  ------------> us_logintime
  `DATE_REGISTER` datetime NOT NULL,  -------------> us_regtime
  `LID` char(2) DEFAULT NULL,
  `PERSONAL_PROFESSION` varchar(255) DEFAULT NULL,
  `PERSONAL_WWW` varchar(255) DEFAULT NULL,
  `PERSONAL_ICQ` varchar(255) DEFAULT NULL,
  `PERSONAL_GENDER` char(1) DEFAULT NULL,
  `PERSONAL_BIRTHDATE` varchar(50) DEFAULT NULL,
  `PERSONAL_PHOTO` int(18) DEFAULT NULL,
  `PERSONAL_PHONE` varchar(255) DEFAULT NULL,
  `PERSONAL_FAX` varchar(255) DEFAULT NULL,
  `PERSONAL_MOBILE` varchar(255) DEFAULT NULL,
  `PERSONAL_PAGER` varchar(255) DEFAULT NULL,
  `PERSONAL_STREET` text,
  `PERSONAL_MAILBOX` varchar(255) DEFAULT NULL,
  `PERSONAL_CITY` varchar(255) DEFAULT NULL,
  `PERSONAL_STATE` varchar(255) DEFAULT NULL,
  `PERSONAL_ZIP` varchar(255) DEFAULT NULL,
  `PERSONAL_COUNTRY` varchar(255) DEFAULT NULL,
  `PERSONAL_NOTES` text,
  `WORK_COMPANY` varchar(255) DEFAULT NULL,
  `WORK_DEPARTMENT` varchar(255) DEFAULT NULL,
  `WORK_POSITION` varchar(255) DEFAULT NULL,  --------------> us_workposition
  `WORK_WWW` varchar(255) DEFAULT NULL,
  `WORK_PHONE` varchar(255) DEFAULT NULL,
  `WORK_FAX` varchar(255) DEFAULT NULL,
  `WORK_PAGER` varchar(255) DEFAULT NULL,
  `WORK_STREET` text,
  `WORK_MAILBOX` varchar(255) DEFAULT NULL,
  `WORK_CITY` varchar(255) DEFAULT NULL,
  `WORK_STATE` varchar(255) DEFAULT NULL,
  `WORK_ZIP` varchar(255) DEFAULT NULL,
  `WORK_COUNTRY` varchar(255) DEFAULT NULL,
  `WORK_PROFILE` text,
  `WORK_LOGO` int(18) DEFAULT NULL,
  `WORK_NOTES` text,
  `ADMIN_NOTES` text,
  `STORED_HASH` varchar(32) DEFAULT NULL,
  `XML_ID` varchar(255) DEFAULT NULL,
  `PERSONAL_BIRTHDAY` date DEFAULT NULL,
  `EXTERNAL_AUTH_ID` varchar(255) DEFAULT NULL,
  `CHECKWORD_TIME` datetime DEFAULT NULL,  --------------------> us_checkwordtime
  `SECOND_NAME` varchar(50) DEFAULT NULL,  --------------------> us_secondname
  `CONFIRM_CODE` varchar(8) DEFAULT NULL,
  `LOGIN_ATTEMPTS` int(18) DEFAULT NULL,
  `LAST_ACTIVITY_DATE` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ix_login` (`LOGIN`,`EXTERNAL_AUTH_ID`),
  KEY `ix_b_user_email` (`EMAIL`),
  KEY `ix_b_user_activity_date` (`LAST_ACTIVITY_DATE`),
  KEY `IX_B_USER_XML_ID` (`XML_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=cp1251;

        */


        // create group table
        $this->createTable('{{%group}}', [
            'group_id' => Schema::TYPE_PK,
            'group_active' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'group_name' => Schema::TYPE_STRING . ' NOT NULL',
            'group_description' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_group_name', '{{%group}}', 'group_name');

        /*

CREATE TABLE `b_group` (
  `ID` int(18) NOT NULL AUTO_INCREMENT,
  `TIMESTAMP_X` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ACTIVE` char(1) NOT NULL DEFAULT 'Y',  ----------------> group_active Y = 1, N = 0
  `C_SORT` int(18) NOT NULL DEFAULT '100',
  `ANONYMOUS` char(1) NOT NULL DEFAULT 'N',
  `NAME` varchar(255) NOT NULL, --------------------------> group_name
  `DESCRIPTION` varchar(255) DEFAULT NULL, ---------------> group_description
  `SECURITY_POLICY` text,
  `STRING_ID` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=cp1251;

        */

        // create users group table
        $this->createTable('{{%usergroup}}', [
            'usgr_id' => Schema::TYPE_PK,
            'usgr_uid' => Schema::TYPE_INTEGER . ' NOT NULL',
            'usgr_gid' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_usgr_uid', '{{%usergroup}}', 'usgr_uid');
        $this->createIndex('idx_usgr_gid', '{{%usergroup}}', 'usgr_gid');

        /*
CREATE TABLE `b_user_group` (
  `USER_ID` int(18) NOT NULL,
  `GROUP_ID` int(18) NOT NULL,
  `DATE_ACTIVE_FROM` datetime DEFAULT NULL,
  `DATE_ACTIVE_TO` datetime DEFAULT NULL,
  UNIQUE KEY `ix_user_group` (`USER_ID`,`GROUP_ID`),
  KEY `ix_user_group_group` (`GROUP_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

        */

        // create message flags table
        $this->createTable('{{%msgflags}}', [
            'fl_id' => Schema::TYPE_PK,
            'fl_name' => Schema::TYPE_STRING . ' NOT NULL',
            'fl_command' => Schema::TYPE_STRING,
            'fl_sort' => Schema::TYPE_INTEGER . ' NOT NULL Default 0',
            'fl_glyth' => Schema::TYPE_STRING,
            'fl_glyth_color' =>  Schema::TYPE_STRING . '(32) Default \'#ff9999\'',
            'fl_sname' =>  Schema::TYPE_STRING . '(16)',
            'fl_duration' => Schema::TYPE_INTEGER . ' Default 14',
        ], $tableOptionsMyISAM);

/*
         CREATE TABLE `b_iblock_property_enum` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `PROPERTY_ID` int(11) NOT NULL,
          `VALUE` varchar(255) NOT NULL,    ----------------> fl_name
          `DEF` char(1) NOT NULL DEFAULT 'N',
          `SORT` int(11) NOT NULL DEFAULT '500', -----------> fl_sort
          `XML_ID` varchar(200) NOT NULL,
          `TMP_ID` varchar(40) DEFAULT NULL,
          PRIMARY KEY (`ID`),
          UNIQUE KEY `ux_iblock_property_enum` (`PROPERTY_ID`,`XML_ID`)
        ) ENGINE=MyISAM AUTO_INCREMENT=155 DEFAULT CHARSET=utf8;

*/

        // create message answers table
        $this->createTable('{{%msganswers}}', [
            'ma_id' => Schema::TYPE_PK,
            'ma_message_id' => Schema::TYPE_INTEGER, //  . ' NOT NULL',
            'ma_user_id' => Schema::TYPE_INTEGER, //  . ' NOT NULL',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_msganswers_user', '{{%msganswers}}', 'ma_user_id');
        $this->createIndex('idx_msganswers_msg', '{{%msganswers}}', 'ma_message_id');
/*
CREATE TABLE `b_iblock_element_prop_m52` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `IBLOCK_ELEMENT_ID` int(11) NOT NULL,  ---------> ma_message_id
  `IBLOCK_PROPERTY_ID` int(11) NOT NULL,
  `VALUE` text NOT NULL,  -------------------------> ma_user_id
  `VALUE_ENUM` int(11) DEFAULT NULL,
  `VALUE_NUM` decimal(18,4) DEFAULT NULL,
  `DESCRIPTION` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ix_iblock_elem_prop_m52_1` (`IBLOCK_ELEMENT_ID`,`IBLOCK_PROPERTY_ID`),
  KEY `ix_iblock_elem_prop_m52_2` (`IBLOCK_PROPERTY_ID`),
  KEY `ix_iblock_elem_prop_m52_3` (`VALUE_ENUM`,`IBLOCK_PROPERTY_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=364 DEFAULT CHARSET=utf8;
 */
        // create message table
        $this->createTable('{{%message}}', [
            'msg_id' => Schema::TYPE_PK,
            'msg_createtime' => Schema::TYPE_DATETIME . '',
            'msg_active' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'msg_pers_name' => Schema::TYPE_STRING . ' NOT NULL',
            'msg_pers_secname' => Schema::TYPE_STRING . '',
            'msg_pers_lastname' => Schema::TYPE_STRING . ' NOT NULL',
            'msg_pers_email' => Schema::TYPE_STRING . ' NOT NULL',
            'msg_pers_phone' => Schema::TYPE_STRING . ' NOT NULL',
            'msg_pers_org' => Schema::TYPE_TEXT . ' NOT NULL',
            'msg_pers_region' => Schema::TYPE_INTEGER . ' Default NULL',
            'msg_pers_text' => Schema::TYPE_TEXT . '',
            'ekis_id' => Schema::TYPE_BIGINT . ' Default NULL',

            'msg_comment' => Schema::TYPE_TEXT,

            'msg_empl_id' => Schema::TYPE_INTEGER,
            'msg_empl_command' => Schema::TYPE_TEXT,
            'msg_empl_remark' => Schema::TYPE_TEXT,

            'msg_answer' => Schema::TYPE_TEXT,
            'msg_answertime' => Schema::TYPE_DATETIME,

            'msg_oldcomment' => Schema::TYPE_STRING,
            'msg_flag' => Schema::TYPE_INTEGER . ' NOT NULL Default 0',
            'msg_subject' => Schema::TYPE_INTEGER,
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_message_flag', '{{%message}}', 'msg_flag');
        $this->createIndex('idx_message_empl', '{{%message}}', 'msg_empl_id');
        $this->createIndex('idx_message_create', '{{%message}}', 'msg_createtime');

/*
CREATE TABLE `b_iblock_element` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TIMESTAMP_X` datetime DEFAULT NULL,
  `MODIFIED_BY` int(18) DEFAULT NULL,
  `DATE_CREATE` datetime DEFAULT NULL,  -----------------------> msg_createtime
  `CREATED_BY` int(18) DEFAULT NULL,
  `IBLOCK_ID` int(11) NOT NULL DEFAULT '0',
  `IBLOCK_SECTION_ID` int(11) DEFAULT NULL,
  `ACTIVE` char(1) NOT NULL DEFAULT 'Y',  ---------------------> msg_active Y = 1, N = 0
  `ACTIVE_FROM` datetime DEFAULT NULL,
  `ACTIVE_TO` datetime DEFAULT NULL,
  `SORT` int(11) NOT NULL DEFAULT '500',
  `NAME` varchar(255) NOT NULL,
  `PREVIEW_PICTURE` int(18) DEFAULT NULL,
  `PREVIEW_TEXT` text,             -----------------------------> msg_pers_text
  `PREVIEW_TEXT_TYPE` varchar(4) NOT NULL DEFAULT 'text',
  `DETAIL_PICTURE` int(18) DEFAULT NULL,
  `DETAIL_TEXT` longtext,  -------------------------------------> msg_answer
  `DETAIL_TEXT_TYPE` varchar(4) NOT NULL DEFAULT 'text',
  `SEARCHABLE_CONTENT` text,
  `WF_STATUS_ID` int(18) DEFAULT '1',
  `WF_PARENT_ELEMENT_ID` int(11) DEFAULT NULL,
  `WF_NEW` char(1) DEFAULT NULL,
  `WF_LOCKED_BY` int(18) DEFAULT NULL,
  `WF_DATE_LOCK` datetime DEFAULT NULL,
  `WF_COMMENTS` text,
  `IN_SECTIONS` char(1) NOT NULL DEFAULT 'N',
  `XML_ID` varchar(255) DEFAULT NULL,
  `CODE` varchar(255) DEFAULT NULL,
  `TAGS` varchar(255) DEFAULT NULL,  ---------------------------> msg_oldcomment
  `TMP_ID` varchar(40) DEFAULT NULL,
  `WF_LAST_HISTORY_ID` int(11) DEFAULT NULL,
  `SHOW_COUNTER` int(18) DEFAULT NULL,
  `SHOW_COUNTER_START` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ix_iblock_element_1` (`IBLOCK_ID`,`IBLOCK_SECTION_ID`),
  KEY `ix_iblock_element_4` (`IBLOCK_ID`,`XML_ID`,`WF_PARENT_ELEMENT_ID`),
  KEY `ix_iblock_element_3` (`WF_PARENT_ELEMENT_ID`),
  KEY `ix_iblock_element_code` (`IBLOCK_ID`,`CODE`)
) ENGINE=MyISAM AUTO_INCREMENT=81971 DEFAULT CHARSET=utf8;

194	Фамилия
195	Имя
196	Отчество
197	Адрес электронной почты
198	Контактный телефон
199	Образовательное учреждение
200	Округ
201	Статус
202	Комментарий модератора
206	Файлы ответчика
207	ID ответчика
214	ID соответчиков
215	Текст поручения
216	Замечание к ответу
217	Должность ответчика

CREATE TABLE `b_iblock_element_prop_s52` (
  `IBLOCK_ELEMENT_ID` int(11) NOT NULL,
  `PROPERTY_194` text,   -----------------------------> msg_pers_lastname
  `DESCRIPTION_194` varchar(255) DEFAULT NULL,
  `PROPERTY_195` text,   -----------------------------> msg_pers_name
  `DESCRIPTION_195` varchar(255) DEFAULT NULL,
  `PROPERTY_196` text,   -----------------------------> msg_pers_secname
  `DESCRIPTION_196` varchar(255) DEFAULT NULL,
  `PROPERTY_197` text,   -----------------------------> msg_pers_email
  `DESCRIPTION_197` varchar(255) DEFAULT NULL,
  `PROPERTY_198` text,   -----------------------------> msg_pers_phone
  `DESCRIPTION_198` varchar(255) DEFAULT NULL,
  `PROPERTY_199` text,   -----------------------------> msg_pers_org
  `DESCRIPTION_199` varchar(255) DEFAULT NULL,
  `PROPERTY_200` int(11) DEFAULT NULL,   -------------> msg_pers_region
  `DESCRIPTION_200` varchar(255) DEFAULT NULL,
  `PROPERTY_201` int(11) DEFAULT NULL,   -------------> msg_flag
  `DESCRIPTION_201` varchar(255) DEFAULT NULL,
  `PROPERTY_202` text,   -----------------------------> msg_comment
  `DESCRIPTION_202` varchar(255) DEFAULT NULL,
  `PROPERTY_206` int(11) DEFAULT NULL,
  `DESCRIPTION_206` varchar(255) DEFAULT NULL,
  `PROPERTY_207` text,   -----------------------------> msg_empl_id
  `DESCRIPTION_207` varchar(255) DEFAULT NULL,
  `PROPERTY_214` text,
  `DESCRIPTION_214` varchar(255) DEFAULT NULL,
  `PROPERTY_215` text,   -----------------------------> msg_empl_command
  `DESCRIPTION_215` varchar(255) DEFAULT NULL,
  `PROPERTY_216` text,   -----------------------------> msg_empl_remark
  `DESCRIPTION_216` varchar(255) DEFAULT NULL,
  `PROPERTY_217` text,
  `DESCRIPTION_217` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`IBLOCK_ELEMENT_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 */

        // create regions table
        $this->createTable('{{%regions}}', [
            'reg_id' => Schema::TYPE_PK,
            'reg_name' => Schema::TYPE_STRING . ' NOT NULL',  // ---------------------> NAME
            'reg_active' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0', // ---------------------> ACTIVE Y = 1, N = 0
        ], $tableOptionsMyISAM);

        $this->createTable('{{%file}}', [
            'file_id' => Schema::TYPE_PK,
            'file_time' => Schema::TYPE_DATETIME,
            'file_orig_name' => Schema::TYPE_STRING . ' NOT NULL',
            'file_msg_id' => Schema::TYPE_INTEGER,
            'file_user_id' => Schema::TYPE_INTEGER,
            'file_size' => Schema::TYPE_INTEGER . ' NOT NULL',
            'file_type' => Schema::TYPE_STRING,
            'file_name' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_file_msg_id', '{{%file}}', 'file_msg_id');
        $this->createIndex('idx_file_name', '{{%file}}', 'file_name');

    }

    public function down()
    {
        $this->dropTable('{{%user}}');
        $this->dropTable('{{%group}}');
        $this->dropTable('{{%usergroup}}');

        $this->dropTable('{{%msgflags}}');
        $this->dropTable('{{%msganswers}}');
        $this->dropTable('{{%message}}');
        $this->dropTable('{{%regions}}');
        $this->dropTable('{{%file}}');

        return true;
    }
}
