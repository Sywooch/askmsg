<?php
/**
 * User: KozminVA
 * Date: 02.03.2015
 */

namespace app\components;

use Yii;
use app\models\User;
use yii\rbac\PhpManager;
use app\models\Rolesimport;

class PhpextManager extends PhpManager {

    /*
     * Строим свою проверку на основе существующей структуры таблиц
     */
    public function checkAccess($userId, $permissionName, $params = []) {
        /** @var User $oUser */
        $bRet = false;

        $oUser = Yii::$app->user->identity;

        if( ($oUser === null) && ($permissionName !== '?') ) {
            return $bRet;
        }

        foreach($oUser->permissions As $ob) {
            Yii::info("checkAccess(): [{$ob->group_id} ? = {$permissionName} || " . Rolesimport::ROLE_ADMIN . ']');
            if( ($ob->group_id == $permissionName) || ($ob->group_id == Rolesimport::ROLE_ADMIN) ) {
                $bRet = true;
                Yii::info("checkAccess(): true [{$ob->group_id} ? = {$permissionName} || " . Rolesimport::ROLE_ADMIN . ']');
                break;
            }
        }
        Yii::info("checkAccess(): ret = " . ($bRet ? 'true' : 'false'));
        return $bRet;

    }
}