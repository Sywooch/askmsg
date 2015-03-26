<?php
/**
 * User: KozminVA
 * Date: 26.03.2015
 * Time: 11:23
 */

namespace app\api\modules\v1\models;

use yii;
use app\models\User;
use app\models\Rolesimport;

class Message extends \app\models\Message {

    public function fields() {
        $aFields =  [
            'msg_id',
            'msg_createtime',
/*
            'msg_pers_text',
            'msg_pers_name',
            'msg_pers_secname',
            'msg_pers_lastname',
*/
            'ekis_id',
            'msg_pers_org',
            'msg_empl_id',
            'msg_answertime',
        ];

        if( !Yii::$app->user->isGuest ) {
            if( $this->isAnswerble || Yii::$app->user->can(Rolesimport::ROLE_MODERATE_DOGM) ) {
                $aFields =  array_merge(
                    $aFields,
                    [
                        'msg_pers_email',
                        'msg_pers_phone',
                        'msg_empl_command',
                        'msg_empl_remark',
                        'msg_answer',
                    ]
                );
            }

        }
        return $aFields;
    }

}