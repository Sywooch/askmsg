<?php
/**
 * User: KozminVA
 * Date: 26.03.2015
 * Time: 11:23
 */

namespace app\api\modules\v1\models;

use yii;
use app\models\Rolesimport;

class User extends \app\models\User {

    public function fields() {
        $aFields =  [
/*
us_chekword_hash
us_regtime
us_checkwordtime
email_confirm_token
password_reset_token
 */

            'us_id',
//            'us_xtime',
            'us_active',
            'us_name',
            'us_secondname',
            'us_lastname',
            'us_workposition',
        ];

        if( !Yii::$app->user->isGuest ) {
            if( Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM) ) {
                $aFields =  array_merge(
                    $aFields,
                    [
                        'us_login',
                        'us_password_hash',
                        'us_email',
                        'us_logintime',
                        'auth_key',
                    ]
                );
            }

        }
        return $aFields;
    }

}