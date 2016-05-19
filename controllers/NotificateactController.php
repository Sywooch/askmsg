<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

use mosedu\multirows\MultirowsBehavior;

use app\models\Message;
use app\models\Notificateact;
use app\models\NotificateactSearch;
use app\models\Rolesimport;
use app\models\Msgflags;
use app\models\MessageSearch;
use app\components\SwiftHeaders;
use app\models\Notificatelog;

/**
 * NotificateactController implements the CRUD actions for Notificateact model.
 */
class NotificateactController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'process', 'send', 'clearnotifylog', /*'create', 'view', 'update', 'delete', 'admin', */],
                        'roles' => [Rolesimport::ROLE_MODERATE_DOGM],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],

            'validatePrmissions' => [
                'class' => MultirowsBehavior::className(),
                'model' => Notificateact::className(),
            ],
        ];
    }

    /**
     * Lists all Notificateact models.
     * @return mixed
     */
    public function actionIndex()
    {
        if( Yii::$app->request->isAjax ) {
            if( isset($_POST['Notificateact']) ) {
                $result = $this->getBehavior('validatePrmissions')->validateData();

                Yii::$app->response->format = Response::FORMAT_JSON;
//                Yii::info('actionIndex(): return json ' . print_r($result, true));

                if (count($result) == 0) {
                    $data = $this->getBehavior('validatePrmissions')->getData();
                    $this->saveActions($data['data']);
                }
                return $result;
            }

            return $this->renderAjax('actlist', []);
        }

        if( Yii::$app->request->post() ) {
            return $this->redirect('/');
        } else {
            return $this->render('updateall', []);
        }


        /*
        $searchModel = new NotificateactSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        */
    }

    /**
     *
     * @param integer $id
     * @return mixed
     */
    public function actionProcess()
    {
        $aActions = Notificateact::find()->where('true')->orderBy('ntfd_message_age')->all();
        $searchModel = new MessageSearch();
        $dataProvider = $searchModel->searchNotificate(Yii::$app->request->queryParams, $this->findMessages($aActions));

        $sKeyName = 'lastclearnotifylog';
        $sToday = date('Ymd');
        if( !Yii::$app->session->has($sKeyName) || (Yii::$app->session->get($sKeyName, 0) < $sToday) ) {
            Notificatelog::clearNotify();
            Yii::$app->session->set($sKeyName, $sToday);
        }

        return $this->render('//message/notifylist', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     *
     * @return mixed
     */
    public function actionClearnotifylog()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['clear' => Notificatelog::clearNotify()];
    }

    /**
     *
     * @param integer $id
     * @return mixed
     */
    public function actionSend()
    {
        $aErr = [];
        $id = Yii::$app->request->post('id', 0);
        /** @var Message $model */
        $model = Message::find()->with('employee', 'curator')->where(['msg_id' => $id])->one();
//        Yii::info('actionSend(): id = ' . $id . ' model = ' . ($model === null ? 'null' : $model->msg_id));

        if( $model !== null ) {
            $days = Notificateact::getAdge($model->msg_createtime);
            $aAct = Notificateact::getDateAct($model->msg_createtime);
//            $aAct = Notificateact::find()->where('ntfd_message_age = '.$days)->all();
            $user = Yii::$app->user->identity;
            $aEmails = [];
            /** @var Notificateact $ob */
            foreach($aAct As $idAct=>$ob) {
                Yii::info('actionSend(): Act = ' . $idAct);
//                $sTemplate = 'notificate_' . $ob->ntfd_operate;
                $sTemplate = 'notificate_' . $idAct;
                $email = '';
/*
                if( ($ob->ntfd_operate == Notificateact::ACTI_EMAIL_EPLOEE) && ($model->employee !== null) ) {
                    $email = $model->employee->us_email;
                }
                else if( ($ob->ntfd_operate == Notificateact::ACTI_EMAIL_CONTROLER) && ($model->curator !== null) ) {
                    $email = $model->curator->us_email;
                }
                else if( $ob->ntfd_operate == Notificateact::ACTI_EMAIL_MODERATOR ) {
                    $email = $user->us_email;
                }
*/
                $bSoglas = in_array($model->msg_flag, [Msgflags::MFLG_SHOW_NOSOGL, Msgflags::MFLG_INT_NOSOGL, ]);

                if( !$bSoglas && ($idAct == Notificateact::ACTI_EMAIL_EPLOEE) && ($model->employee !== null) ) {
                    $email = $model->employee->us_email;
                }
                else if( $bSoglas && ($idAct == Notificateact::ACTI_EMAIL_CONTROLER) && ($model->curator !== null) ) {
                    $email = $model->curator->us_email;
                }
                else if( $idAct == Notificateact::ACTI_EMAIL_MODERATOR ) {
                    $email = $user->us_email;
                }
//                Yii::info('actionSend(): email = ' . $email);
                if( $email != '' ) {
                    $oMsg = Yii::$app->mailer->compose($sTemplate, ['model' => $model, 'user' => $user, ])
                        ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                        ->setTo($email)
                        ->setSubject('Напоминание об обращении №' . $model->msg_id . ' от ' . date('d.m.Y', strtotime($model->msg_createtime)));

                    SwiftHeaders::setAntiSpamHeaders($oMsg, ['email' => Yii::$app->params['supportEmail']]);
                    $aEmails[] = $oMsg;
                }
                else {
                    Yii::info('actionSend(): Error Not found email ['.$id.'] for act [' . $idAct . ']');
//                    Yii::info('actionSend(): Error Not found email ['.$id.'] for act [' . $ob->ntfd_operate . ']');
//                    $aErr['error'] = ['message' => 'Not found email ['.$id.'] for act [' . $ob->ntfd_operate . ']'];
                }
            }
            if( count($aEmails) > 0 ) {
//                Yii::info('actionSend(): count(aEmails) = ' . count($aEmails));
                Notificatelog::clearNotify();
                Notificatelog::addNotify($id);
                Yii::$app->mailer->sendMultiple($aEmails);
                $aErr['data'] = ['message' => 'Message ' . $id . ' send ' . count($aEmails) . ' emails'];
            }
        }
        else {
            $aErr['error'] = ['message' => 'Not found message ['.$id.']'];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $aErr; // $_POST;
    }

    /**
     * Displays a single Notificateact model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Notificateact model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Notificateact();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ntfd_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Notificateact model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ntfd_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Notificateact model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Notificateact model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Notificateact the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Notificateact::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Сохраняем список
     * @param array $aData
     */
    public function saveActions($aData) {
        Yii::info('saveActions() aData = ' . print_r($aData, true));
        $aModels = Notificateact::find()->where('true')->orderBy('ntfd_message_age')->all();
        $aNeedDel = [];
        foreach($aModels As $oAction) {
            $nIndex = $this->searchAction($aData, $oAction->ntfd_message_age, $oAction->ntfd_operate);
            if( $nIndex !== -1 ) {
                // все нормально, запись остается нужно флаг поменять
//                Yii::info('saveActions save ' . $oAction->udat_id . '['.$nIndex.'] permission = ' . $aData[$nIndex]['udat_role_id']);
                $oAction->attributes = $aData[$nIndex];
                $oAction->save();
                unset($aData[$nIndex]);
            }
            else {
//                Yii::info('savePermissions('.$oAction->udat_id.'): need del');
                $aNeedDel[] = $oAction;
            }
        }

        // оставшиеся данные пытаемся запихать в существующие записи, которые должны быть удалены
        $oAction = reset($aNeedDel);
        foreach($aData As $k=>$data) {
            if( $oAction === false ) {
                // кончились удаляемые записи - создаем новые
                $oAction = new Notificateact();
            }
            Yii::info('saveActions() data ['.$k.'] = ' . print_r($data, true));
            $oAction->attributes = [
                'ntfd_message_age' => $data['ntfd_message_age'],
                'ntfd_operate' => $data['ntfd_operate'],
//                'ntfd_flag' => $data['ntfd_flag'],
            ];

            if( !$oAction->save() ) {
                Yii::error("saveActions() Error save: " . print_r($oAction->getErrors(), true));
            }
            $oAction = next($aNeedDel);
        }

        // если остались удаляемые записи - удаляем их
        while( $oAction !== false ) {
            $oAction->delete();
            $oAction = next($aNeedDel);
        }

    }

    /**
     * @param array $data post data
     * @param int $age age for search
     * @param int $operate operate id for search
     * @return int index in data
     */
    public function searchAction($data, $age, $operate) {
        $index = -1;
        foreach($data As $k=>$v) {
            if( ($v['ntfd_message_age'] == $age)
             && ($v['ntfd_operate'] == $operate) ) {
                $index = $k;
                break;
            }
        }
        return $index;
    }

    /**
     * @param array $aActions Notificateact
     */
    public function findMessages($aActions) {
//        $n24 = 86400; // 24 * 3600
//        $tToday = mktime(0, 0, 0);
//        $tToday = mktime(0, 0, 0, 3, date("j"), date('Y')) - $n24;
        $tToday = Notificateact::getToday();

        /** @var Notificateact $ob */
        $sWhere = '';
        $aFlags = [
//            Msgflags::MFLG_INT_NEWANSWER,
//            Msgflags::MFLG_SHOW_NEWANSWER,
            Msgflags::MFLG_INT_REVIS_INSTR,
            Msgflags::MFLG_INT_INSTR,
            Msgflags::MFLG_SHOW_INSTR,
            Msgflags::MFLG_SHOW_REVIS,
            Msgflags::MFLG_SHOW_NOSOGL,
            Msgflags::MFLG_INT_NOSOGL,

        ];
        foreach($aActions As $ob) {
            $t1 = $tToday - $ob->ntfd_message_age * Notificateact::DAY_DURATION;
            $t2 = $t1 + Notificateact::DAY_DURATION;
            $sWhere .= ($sWhere == '' ? '' : ' Or ') . '(' . (($ob->ntfd_flag & 1) > 0 ? '' : 'msg_createtime >= \''.date('Y-m-d H:i:s', $t1).'\' And') . ' msg_createtime < \''.date('Y-m-d H:i:s', $t2).'\'' . ' /* '.$ob->ntfd_message_age . (($ob->ntfd_flag & 1) > 0 ? '+' : '') . ' */ )';
        }
        if( $sWhere == '' ) {
            $sWhere = 'FALSE';
        }
        $sWhere = "({$sWhere}) And msg_flag In (" . implode(',', $aFlags) . ")";
        Yii::info('findMessages(): ' . $sWhere);
        return $sWhere;
    }
}
