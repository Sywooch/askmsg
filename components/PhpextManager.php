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

        $oUser = Yii::$app->user->identity;
        if( $oUser === null && $permissionName !== '?' ) {
            return false;
        }

        foreach($oUser->permissions As $ob) {
            if( $ob->group_id == $permissionName || $ob->group_id == Rolesimport::ROLE_ADMIN ) {
                return true;
            }
        }
        return false;

    }
}