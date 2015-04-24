<?php

use yii\db\Schema;
use yii\db\Migration;

use Httpful\Request;
use Httpful\Response;

class m150422_151426_change_regions extends Migration
{
    public function up()
    {
        $aConvert = [
            1 => 7,
            2 => 4,
            3 => 5,
            4 => 1,
            5 => 11,
            6 => 8,
            7 => 3,
            8 => 2,
            9 => 6,
            10 => 10,
            11 => 2,
            12 => 12,
            13 => 8,
        ];

        // http://hastur.temocenter.ru/task/eo.dict/list/eo_district_name - получаем списки районов
        $request = Request::post('http://hastur.temocenter.ru/task/eo.dict/list/eo_district_name')
        ->addHeader('Accept', 'application/json; charset=UTF-8');
//            ->body(http_build_query($data))
//            ->contentType('application/x-www-form-urlencoded');

        /** @var Response $response */
        $response = $request->send();
        $aData = json_decode($response->body, true);
        $sOut = '';
        $aNewRegValues = [];
        if( isset($aData['eo_district_name']) && (count($aData['eo_district_name']) > 0) ) {
            $sOut .= "Regions [".count($aData['eo_district_name'])."]:\n";
            foreach($aData['eo_district_name'] As $ob) {
                $sOut .= "{$ob['eo_district_name_id']} : {$ob['eo_district_name']}\n";
                $aNewRegValues[] = "({$ob['eo_district_name_id']}, '{$ob['eo_district_name']}', 1)";
            }
        }
        else {
            $sOut = "There are no regions in EKIS\n";
        }

        echo  iconv('UTF-8','CP866', $sOut);
        Yii::info($sOut);

        $db = Yii::$app->db;

        $sSql = 'SELECT COUNT(*) FROM educom_message Where ekis_id Is Null';
        $nOldRecords = $db->createCommand($sSql)->queryScalar();

        $sSql = 'SELECT COUNT(*) FROM educom_message Where ekis_id Is Not Null';
        $nNewRecords = $db->createCommand($sSql)->queryScalar();
        $sOut = "New = {$nNewRecords} old = {$nOldRecords}\n";

        echo  iconv('UTF-8','CP866', $sOut);
        Yii::info($sOut);

        $sSql = 'Select r.reg_id, r.reg_name, COUNT(m.msg_id) As cou
            From `educom_regions` r, educom_message m
            Where m.ekis_id Is Null And m.msg_pers_region = r.reg_id
            Group By r.reg_id, r.reg_name
            Order By r.reg_id';
        $aOldData = $db->createCommand($sSql)->queryAll();

        $sOut = '';
        $nOldCou = 0;
        foreach($aOldData As $ob) {
            $sOut .= "{$ob['reg_id']}\t{$ob['reg_name']}\t{$ob['cou']}\n";
            $nOldCou += $ob['cou'];
        }
        $sOut .= "SUmm:\t --> \t{$nOldCou}\n";

        echo  iconv('UTF-8','CP866', $sOut);
        Yii::info($sOut);

        foreach($aConvert As $k=>$v) {
            $s = '';
            foreach($aOldData As $ob) {
                if( $ob['reg_id'] == $k ) {
                    $s .= $ob['reg_name'];
                }
            }
            foreach($aData['eo_district_name'] As $ob) {
                if( $ob['eo_district_name_id'] == $v ) {
                    $s .= ' -> ' . $ob['eo_district_name'] . "\n";
                    break;
                }
            }
            echo  iconv('UTF-8','CP866', $s);
            Yii::info($s);
            $s = '';
        }

        $sSql = 'Update educom_regions Set reg_id = 100 + reg_id Where reg_id < 100';
        $nUpd = $db->createCommand($sSql)->execute();

        $sOut = 'Updates: ' . $nUpd . " : " . $sSql . "\n";
        echo  iconv('UTF-8','CP866', $sOut);
        Yii::info($sOut);

        $sSql = 'Update educom_message Set msg_pers_region = 100 + msg_pers_region Where ekis_id Is Null';
        $nUpd = $db->createCommand($sSql)->execute();
        $sOut = 'Updates: ' . $nUpd . " : " . $sSql . "\n";
        echo  iconv('UTF-8','CP866', $sOut);
        Yii::info($sOut);

        $sSql = 'Select r.reg_id, r.reg_name, COUNT(m.msg_id) As cou
            From `educom_regions` r, educom_message m
            Where m.ekis_id Is Null And m.msg_pers_region = r.reg_id
            Group By r.reg_id, r.reg_name
            Order By r.reg_id';
        $aNewData = $db->createCommand($sSql)->queryAll();

        $sOut = '';
        $nNewCou = 0;
        foreach($aNewData As $ob) {
            $sOut .= "{$ob['reg_id']}\t{$ob['reg_name']}\t{$ob['cou']}\n";
            $nNewCou += $ob['cou'];
        }
        $sOut .= "SUmm:\t --> \t{$nNewCou}\n";

        echo  iconv('UTF-8','CP866', $sOut);
        Yii::info($sOut);

        $sSql = 'Insert Into educom_regions (reg_id, reg_name, reg_active) Values ' . implode(',', $aNewRegValues);
        $nUpd = $db->createCommand($sSql)->execute();
        $sOut = 'Updates: ' . $nUpd . " : " . $sSql . "\n";
        echo  iconv('UTF-8','CP866', $sOut);
        Yii::info($sOut);

        $sOut = '';
        foreach($aConvert As $idOld => $idNew) {
            $sSql = 'Update educom_message Set msg_pers_region = ' . $idNew . ' Where msg_pers_region = ' . (100 + $idOld);
            $nUpd = $db->createCommand($sSql)->execute();
            $sOut .= 'Updates: ' . $nUpd . " : " . $sSql . "\n";
        }

        echo  iconv('UTF-8','CP866', $sOut);
        Yii::info($sOut);

        $sSql = 'Select r.reg_id, r.reg_name, COUNT(m.msg_id) As cou
            From `educom_regions` r, educom_message m
            Where m.ekis_id Is Null And m.msg_pers_region = r.reg_id
            Group By r.reg_id, r.reg_name
            Order By r.reg_id';
        $aNewData = $db->createCommand($sSql)->queryAll();

        $sOut = '';
        $nNewCou = 0;
        foreach($aNewData As $ob) {
            $sOut .= "{$ob['reg_id']}\t{$ob['reg_name']}\t{$ob['cou']}\n";
            $nNewCou += $ob['cou'];
        }
        $sOut .= "SUmm:\t --> \t{$nNewCou}\n";

        echo  iconv('UTF-8','CP866', $sOut);
        Yii::info($sOut);

//        return false;
    }

    public function down()
    {
        // echo "m150422_151426_change_regions cannot be reverted.\n";
        $aConvert = [
            1 => 7,
            2 => 4,
            3 => 5,
            4 => 1,
            5 => 11,
            6 => 8,
            7 => 3,
            8 => 2,
            9 => 6,
            10 => 10,
            11 => 2,
            12 => 12,
            13 => 8,
        ];

        $db = Yii::$app->db;

        $sSql = 'Delete From educom_regions Where reg_id < 100';
        $nUpd = $db->createCommand($sSql)->execute();
        $sOut = 'Updates: ' . $nUpd . " : " . $sSql . "\n";
        echo  iconv('UTF-8','CP866', $sSql);
        Yii::info($sSql);

        $sSql = 'Update educom_regions Set reg_id = reg_id - 100 Where reg_id > 100';
        $nUpd = $db->createCommand($sSql)->execute();

        $sOut = 'Updates: ' . $nUpd . " : " . $sSql . "\n";
        echo  iconv('UTF-8','CP866', $sOut);
        Yii::info($sOut);

        $sSql = 'Update educom_message Set msg_pers_region = 100 + msg_pers_region Where ekis_id Is Null';
        $nUpd = $db->createCommand($sSql)->execute();
        $sOut = 'Updates: ' . $nUpd . " : " . $sSql . "\n";
        echo  iconv('UTF-8','CP866', $sOut);
        Yii::info($sOut);

        $sOut = '';
        foreach($aConvert As $idOld => $idNew) {
            $sSql = 'Update educom_message Set msg_pers_region = ' . $idOld . ' Where msg_pers_region = ' . ($idNew + 100);
            $nUpd = $db->createCommand($sSql)->execute();
            $sOut .= 'Updates: ' . $nUpd . " : " . $sSql . "\n";
        }

        echo  iconv('UTF-8','CP866', $sOut);
        Yii::info($sOut);

        $sSql = 'Select r.reg_id, r.reg_name, COUNT(m.msg_id) As cou
            From `educom_regions` r, educom_message m
            Where m.ekis_id Is Null And m.msg_pers_region = r.reg_id
            Group By r.reg_id, r.reg_name
            Order By r.reg_id';
        $aNewData = $db->createCommand($sSql)->queryAll();

        $sOut = '';
        $nNewCou = 0;
        foreach($aNewData As $ob) {
            $sOut .= "{$ob['reg_id']}\t{$ob['reg_name']}\t{$ob['cou']}\n";
            $nNewCou += $ob['cou'];
        }
        $sOut .= "SUmm:\t --> \t{$nNewCou}\n";

        echo  iconv('UTF-8','CP866', $sOut);
        Yii::info($sOut);

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
