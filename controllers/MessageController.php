<?php

namespace app\controllers;

use app\models\Mediateanswer;
use app\models\Msgflags;
use app\models\SendmsgForm;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;

use app\models\Rolesimport;
use app\models\Message;
use app\models\MessageSearch;
use app\components\WaitTrait;
use app\components\ExportMessagesAction;
use app\models\ExportdataForm;

/**
 * MessageController implements the CRUD actions for Message model.
 */
class MessageController extends Controller
{
    use WaitTrait;

    public $defaultAction = 'create';


    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'list', 'create', 'view', 'export', 'captcha', 'mark', 'rating', ],
                        'roles' => ['?', '@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['toword', 'send', 'curatortest', 'mediatecurator', ],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'delete', 'moderatelist', 'upload', 'instruction', 'testmail', 'exportdata', 'mediatemoderate', ],
                        'roles' => [Rolesimport::ROLE_MODERATE_DOGM],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['answerlist', 'answer', 'mediateanswer'],
                        'roles' => [Rolesimport::ROLE_ANSWER_DOGM],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['admin'],
                        'roles' => [Rolesimport::ROLE_ADMIN],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength' => 2,
                'maxLength' => 3,
            ],
            'exportdata' => ExportMessagesAction::className(),
        ];
    }


    /**
     * Lists all Message models.
     * @return mixed
     */
    public function actionAdmin()
    {
        $searchModel = new MessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'action' => ['admin'],
        ]);
    }

    /**
     * Export data to file
     * @return mixed
     */
    public function actionExport()
    {
        $searchModel = new MessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $format = Yii::$app->request->getQueryParam('format', 'xlsx');
        $allfields = Yii::$app->request->getQueryParam('allfields', 0);

        return $this->render(
            (substr($format, 0, 3) == 'doc') ? 'export-doc' : ($allfields ? 'export-wt-all' : 'export-wt-1'),
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'format' => $format,
            ]
        );
    }

    /**
     * Lists all last instruction
     * @return mixed
     */
    public function actionInstruction()
    {
        $param = [
            'term' => Yii::$app->request->getQueryParam('term', ''),
            'limit' => Yii::$app->request->getQueryParam('limit', 10),
            'offset' => Yii::$app->request->getQueryParam('start', 0),
        ];
        $searchModel = new MessageSearch();

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $searchModel->instructionList($param);
    }

    /**
     * Lists all Message models for users.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->actionList();
    }

    /**
     * Lists all Message models for
     * @return mixed
     */
    public function actionModeratelist()
    {
        $searchModel = new MessageSearch();
        $searchModel->msgflags = Message::getMessageFilters()[Rolesimport::ROLE_MODERATE_DOGM];
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider = $searchModel->moderateSearch(Yii::$app->request->queryParams);

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'action' => ['moderatelist'],
        ]);
    }

    /**
     * Lists all Message models for
     * @return mixed
     */
    public function actionAnswerlist()
    {

        $searchModel = new MessageSearch();

        $searchModel->msgflags = Message::getMessageFilters()[Rolesimport::ROLE_ANSWER_DOGM];
        $searchModel->msg_empl_id = Yii::$app->user->identity->getId();
        $searchModel->answers = [Yii::$app->user->identity->getId()];

        $params = Yii::$app->request->queryParams;
//        $sname = (new \ReflectionClass($searchModel))->getShortName();
        $sname = $searchModel->formName();

        $aExcl = array('msg_empl_id');

        foreach($aExcl As $v) {
            $sField = $sname . '[' . $v . ']';
            if( isset($params[$sname]) && isset($params[$sname][$v]) ) {
                unset($params[$sname][$v]);
            }
        }

        $dataProvider = $searchModel->searchanswer($params);

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'action' => ['answerlist'],
        ]);
    }

    /**
     * Lists all Message models for users.
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new MessageSearch();
        $searchModel->msgflags = Message::getMessageFilters()[Rolesimport::ROLE_GUEST];
        $searchModel->scenario = 'index';
        $dataProvider = $searchModel->searchindex(Yii::$app->request->queryParams);

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Message model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if( Yii::$app->request->isAjax || Yii::$app->request->isPost ) {
            return $this->renderPartial('_view01', [
                    'model' => $model,
                ]);
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Message model.
     * @param integer $id
     * @return mixed
     */
    public function actionToword($id)
    {
        $model = $this->findModel($id);
        $sf = $this->renderPartial('msgtoword', [
            'model' => $model,
        ]);
        return Yii::$app->response->sendFile($sf);

    }

    /**
     * Upload file to message
     * @param integer $id
     * @return mixed
     */
    public function actionUpload($id)
    { // TODO: пока не используется, возможно понадобится при автоматической загрузке файлов ответчиком
        // $model = $this->findModel($id);
        return '';
    }

    /**
     * Creates a new Message model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->actionUpdate(0);
    }

    /**
     * Answer
     * @return mixed
     */
    public function actionAnswer($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'answer';

        if( !$model->isAnswerble ) {
            $code = 0;
            if( ($model->msg_flag == Msgflags::MFLG_SHOW_NOSOGL) || ($model->msg_flag == Msgflags::MFLG_INT_NOSOGL) ) {
                $code = Message::EXCAPTION_CODE_MSG_ON_SOGL;
            }
            elseif( ($model->msg_flag == Msgflags::MFLG_SHOW_NEWANSWER) || ($model->msg_flag == Msgflags::MFLG_INT_NEWANSWER) ) {
                $code = Message::EXCAPTION_CODE_MSG_ON_MODARATE;
            }
            throw new ForbiddenHttpException('Message is not editable', $code);
        }

        if( $model->hasMediateanswer() && !$model->isMediateanswerFinished() ) {
            return $this->redirect(['message/mediateanswer', 'id'=>$model->msg_id, ]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->uploadFiles();
            if( !$model->isAnswerble ) {
                Yii::$app->getSession()->setFlash('error', 'Ваш ответ отправлен на проверку модератору.');
            }

            return $this->redirect(['answerlist']);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Mediateanswer
     *
     * @param integer $id
     * @return mixed
     */
    public function actionMediateanswer($id)
    {
        $oMessage = $this->findModel($id);

        if( !$oMessage->isAnswerble ) {
            $code = 0;
            if( ($oMessage->msg_flag == Msgflags::MFLG_SHOW_NOSOGL) || ($oMessage->msg_flag == Msgflags::MFLG_INT_NOSOGL) ) {
                $code = Message::EXCAPTION_CODE_MSG_ON_SOGL;
            }
            elseif( ($oMessage->msg_flag == Msgflags::MFLG_SHOW_NEWANSWER) || ($oMessage->msg_flag == Msgflags::MFLG_INT_NEWANSWER) ) {
                $code = Message::EXCAPTION_CODE_MSG_ON_MODARATE;
            }
            throw new ForbiddenHttpException('Message is not editable', $code);
        }

        $model = Mediateanswer::getMediateAnswer($oMessage);
        $model->msg_flag = $oMessage->msg_flag;

        if ($model->load(Yii::$app->request->post()) ) {
            $model->ma_msg_id = $oMessage->msg_id;
//            return $this->renderContent(nl2br(print_r($model->attributes, true)) . "<br />\n" . $model->msg_flag);
            if( $model->save() ) {
                // обновляем у сообщения флажок и ставим id промежуточного ответа
                $b = $model->setMessageData($oMessage);

                if( $b && !$oMessage->isAnswerble ) {
                    Yii::$app->getSession()->setFlash('error', 'Ваш ответ отправлен на проверку модератору.');
                }

                return $this->redirect(['answerlist']);
            }
        }

        return $this->render('//mediateanswer/update', [
            'model' => $model,
            'message' => $oMessage,
        ]);
    }

    /**
     * Answer
     * @return mixed
     */
    public function actionSend($id)
    {
        $model = $this->findModel($id);
        $form = new SendmsgForm();

//        Yii::info('actionSend('.$id.'): ' . (Yii::$app->request->isAjax ? 'ajax' : 'noajax'));
//        Yii::info('actionSend('.$id.'): ' . ($model->load(Yii::$app->request->post()) ? 'load' : 'noload') . ' POST: ' . print_r(Yii::$app->request->post(), true));
//        Yii::info('actionSend('.$id.'): ' . ($model->load(Yii::$app->request->post()) ? 'load' : 'noload') . ' POST: ' . print_r(Yii::$app->request->post(), true));
        if( Yii::$app->request->isAjax && $form->load(Yii::$app->request->post()) ) {
            Yii::$app->response->format = Response::FORMAT_JSON;
//            Yii::info('actionSend('.$id.'): return json ' . print_r(ActiveForm::validate($model), true));
            return ActiveForm::validate($form);
/*            $sf = $this->renderPartial('msgtoword', [
                'model' => $model,
            ]);
            return Yii::$app->response->sendFile($sf);
*/
        }
//        Yii::info('actionSend('.$id.'): return render form');


/*
        if ( $form->load(Yii::$app->request->post()) ) {
            return $this->redirect(['answerlist']);
        }
*/
        if( Yii::$app->request->isAjax ) {
            return $this->renderAjax('send', [
                'model' => $form,
                'message' => $model,
            ]);
        }
        else {
            return $this->render('send', [
                'model' => $form,
                'message' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Message model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * Новое сообщение создает только посетитель, изменяет - модератор
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id = 0)
    {
        if( $id == 0 ) {
            $model = new Message();
            $model->scenario = 'person';

            $nSubjectId = Yii::$app->request->getQueryParam('subid', null);
            if( $nSubjectId == 254 ) {
                $model->msg_subject = $nSubjectId;
                $model->msg_pers_text = "Здравствуйте,\n\nЯ хотел бы задать вопрос по заработной плате педагогов:\n\n";
            }
/*
            if( Yii::$app->session->has('parent-msg-id') ) {
                // была переадресация с оценки предыдущего сообщения
                // нужно заполнить данные просителя из предыдущего сообщения
                try {
                    $oldModel = $this->findModel(Yii::$app->session->get('parent-msg-id', 0));
                    $aAtr = [
                        'msg_pers_name',
                        'msg_pers_lastname',
                        'msg_pers_email',
                        'msg_pers_phone',
                        'msg_pers_secname',
                        'msg_pers_org',
                        'msg_pers_region',
                        'msg_subject',
                        'ekis_id',
                    ];
                    foreach($aAtr As $v) {
                        $model->{$v} = $oldModel->{$v};
                    }
                    $n = 72;
                    $model->msg_pers_text = "Здравствуйте.\nНа мое обращение № {$oldModel->msg_id} от "
                        . date('d.m.Y', strtotime($oldModel->msg_createtime))
                        . " был получен следующий ответ (автор ответа - "
                        . $oldModel->employee->getFullName()
                        . "):\n\n"
                        . str_pad(' Начало ответа ', $n, "-", STR_PAD_BOTH)
                        . "\n"
                        . trim(strip_tags(str_replace(['</p>', '<br'], ["</p>\n", "\n<br"], $oldModel->msg_answer)))
                        . "\n"
                        . str_pad(' Окончание ответа ', $n, "-", STR_PAD_BOTH)
                        . "\n";
                }
                catch(Exception $e) {
                    //
                }
            }
*/
        }
        else {
            $model = $this->findModel($id);
            if( $model->hasMediateanswer() && !$model->isMediateanswerFinished() ) {
                return $this->redirect(['message/mediatemoderate', 'id' => $id, ]);
            }
            $model->scenario = 'moderator';
            if( $model->msg_empl_id !== null ) {
                $model->employer = $model->employee->getFullName();
            }
            // поправим после сохранения сообщения всех соответчиков
            $model->on(ActiveRecord::EVENT_AFTER_UPDATE, [$model, 'saveCoanswers'] );
            $model->on(ActiveRecord::EVENT_AFTER_UPDATE, [$model, 'saveAlltags'] );
        }

        if ( $model->load(Yii::$app->request->post()) ) {

            $this->DoDelay('msgform.delay.time');

            if( $model->save() ) {
                $model->uploadFiles();
                if( Yii::$app->session->has('parent-msg-id') ) {
                    // удаляем номер сообщения, на которое пишем ответ
                    Yii::$app->session->remove('parent-msg-id');
                }
                if( $model->scenario == 'person' ) {

                    return $this->render(
                        'thankyou',
                        [
                            'model' => $model,
                        ]
                    );
                }
                else {
                    if( !isset($_POST['savebutton']) ) {
                        return $this->redirect(['moderatelist']);
                    }
                    $this->refresh();
                    return;
//                    Yii::info("model->refresh()");
//                    $model->refresh();
                }
            }

        }
//        else {
//            Yii::info('MESSAGE ERROR: ' . print_r($model->getErrors(), true));
            return $this->render('create', [
                'model' => $model,
            ]);
//        }
    }

    /**
     * Curator tests existing Message model.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionCuratortest($id = 0)
    {
        $model = $this->findModel($id);
        $isAdmin = Yii::$app->user->can(Rolesimport::ROLE_ADMIN);
        $notTest = !in_array($model->msg_flag, [Msgflags::MFLG_SHOW_NOSOGL, Msgflags::MFLG_INT_NOSOGL, ]);
        if( $notTest || (($model->msg_curator_id !== Yii::$app->user->getId()) && !$isAdmin) ) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        if( $model->hasMediateanswer() && !$model->isMediateanswerFinished() ) {
            return $this->redirect(['message/mediatecurator', 'id' => $id, ]);
        }


        $model->scenario = 'curatortest';
        if( $model->msg_empl_id !== null ) {
            $model->employer = $model->employee->getFullName();
        }

        if ( $model->load(Yii::$app->request->post()) ) {

            if( $model->save() ) {
                return $this->render(
                    'curatortested',
                    [
                        'model' => $model,
                    ]
                );
            }

        }
//        else {
//            Yii::info('MESSAGE ERROR: ' . print_r($model->getErrors(), true));
            return $this->render('_form_curator', [
                'model' => $model,
            ]);
//        }
    }

    /**
     * Mediatecurator
     *
     * @param integer $id
     * @return mixed
     */
    public function actionMediatecurator($id)
    {
        $oMessage = $this->findModel($id);
        $isAdmin = Yii::$app->user->can(Rolesimport::ROLE_ADMIN);
        $notTest = !in_array(
            $oMessage->msg_flag,
            [Msgflags::MFLG_SHOW_NOSOGL, Msgflags::MFLG_INT_NOSOGL, ]
        );
        if( $notTest || (($oMessage->msg_curator_id !== Yii::$app->user->getId()) && !$isAdmin) ) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if( $oMessage->msg_empl_id !== null ) {
            $oMessage->employer = $oMessage->employee->getFullName();
        }

        $model = Mediateanswer::getMediateAnswer($oMessage);

        if ($model->load(Yii::$app->request->post()) ) {
            $model->ma_msg_id = $oMessage->msg_id;

//            $model->validate();
//            return $this->renderContent(nl2br(print_r($model->getErrors(), true)));
//            return $this->renderContent(nl2br(print_r($model->attributes, true)) . "<br />\n" . $model->msg_flag);
            if( $model->save() ) {
                // обновляем у сообщения флажок и ставим id промежуточного ответа
                $b = $model->setMessageData($oMessage);

                return $this->render(
                    'curatortested',
                    [
                        'model' => $oMessage,
                    ]
                );
            }
        }

        return $this->render('//mediateanswer/moderate', [
            'model' => $model,
            'message' => $oMessage,
        ]);
    }

    /**
     * Mediatemoderate
     *
     * @param integer $id
     * @return mixed
     */
    public function actionMediatemoderate($id)
    {
        $oMessage = $this->findModel($id);
        $notTest = !in_array(
            $oMessage->msg_flag,
            [Msgflags::MFLG_SHOW_NEWANSWER, Msgflags::MFLG_INT_NEWANSWER, ]
        );
        if( $notTest ) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if( $oMessage->msg_empl_id !== null ) {
            $oMessage->employer = $oMessage->employee->getFullName();
        }

        $model = Mediateanswer::getMediateAnswer($oMessage);

        if ($model->load(Yii::$app->request->post()) ) {
            $model->ma_msg_id = $oMessage->msg_id;

//            return $this->renderContent(nl2br(print_r($model->attributes, true)) . "<br />\n" . $model->msg_flag);
            if( $model->save() ) {
                // обновляем у сообщения флажок и ставим id промежуточного ответа
                $b = $model->setMessageData($oMessage);

                return $this->redirect(['moderatelist']);
            }
        }

        return $this->render('//mediateanswer/moderate', [
            'model' => $model,
            'message' => $oMessage,
        ]);
    }

    /**
     * Deletes an existing Message model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        // $this->findModel($id)->delete();
        $model = $this->findModel($id);
        $model->scenario = 'delete';
        $model->msg_flag = $model->msg_flag == Msgflags::MFLG_NOSHOW ? Msgflags::MFLG_NEW : Msgflags::MFLG_NOSHOW;
        $model->save();

        return $this->redirect(Yii::$app->request->getReferrer());
    }

    /**
     * Test email sending
     * @param integer $id
     * @return mixed
     */
    public function actionTestmail($id)
    {
        // $this->findModel($id)->delete();
        $model = $this->findModel($id);

        $from = Yii::$app->request->getQueryParam('from', Msgflags::MFLG_NEW);
        $to = Yii::$app->request->getQueryParam('to', Msgflags::MFLG_SHOW_INSTR);

        $model->_oldAttributes['msg_flag'] = $from;
        $model->msg_flag = $to;
        if( $model->save(false, ['msg_id']) ) {
            $s = 'OK';
        }
        else {
            $s = print_r($model->getErrors(), true);
        }

        return str_replace("\n", "<br />\n", Html::encode($s));
    }

    /**
     * Mark answer by customer
     * @return mixed
     */
    public function actionMark()
    {
//        $model = $this->findModel($id);
        $model = Message::findModelFromMarkUrl() ;
        if( $model === null ) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        if(    ($model->msg_mark !== null)
            || (
                   ($model->msg_flag != Msgflags::MFLG_SHOW_ANSWER)
                && ($model->msg_flag != Msgflags::MFLG_INT_FIN_INSTR)
                )
        ) {
            return $this->render('mark-notallow', [
                'model' => $model,
            ]);
        }

        $model->scenario = 'mark';
        $model->msg_mark = Yii::$app->request->getQueryParam('mark', 5);
        if( !isset($model->aMark[$model->msg_mark]) ) {
            $model->msg_mark = 0;
        }

        if( Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) ) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $aValidate = ActiveForm::validate($model);
            return $aValidate;
        }

        if ( $model->load(Yii::$app->request->post()) ) {
            $this->DoDelay('msgform.delay.time');
            if( $model->save() ) {
                $sMsg = '';
                if( $model->msg_mark != 5 ) {
                    // сохраняем новое обращение с причиной неудовлетворенности
                    $newModel = new Message();
                    $newModel->scenario = 'person';
                    $aAtr = [
                        'msg_pers_name',
                        'msg_pers_lastname',
                        'msg_pers_email',
                        'msg_pers_phone',
                        'msg_pers_secname',
                        'msg_pers_org',
                        'msg_pers_region',
                        'msg_subject',
                        'ekis_id',
                    ];
                    foreach($aAtr As $v) {
                        $newModel->{$v} = $model->{$v};
                    }
                    $n = 72;
                    $newModel->msg_pers_text = "Здравствуйте.\nНа мое обращение № {$model->msg_id} от "
                        . date('d.m.Y', strtotime($model->msg_createtime))
                        . " был получен следующий ответ (автор ответа - "
                        . $model->employee->getFullName()
                        . "):\n\n"
                        . str_pad(' Начало ответа ', $n, "-", STR_PAD_BOTH)
                        . "\n"
                        . trim(strip_tags(str_replace(['</p>', '<br'], ["</p>\n", "\n<br"], $model->msg_answer)))
                        . "\n"
                        . str_pad(' Окончание ответа ', $n, "-", STR_PAD_BOTH)
                        . "\n\n"
                        . "Меня не удовлетворяет данный ответ по следующей причине: \n\n"
                        . $model->marktext
                        . "\n\n";
                    if( $newModel->save() ) {
                        $newModel->uploadFiles();
                    }
                    else {
                        $sMsg = print_r($newModel->getErrors(), true);
                    }
                }
                return $this->render('mark-ok', [
                    'model' => $model,
                    'msg' => $sMsg,
                ]);
            }
        }

        return $this->render('mark', [
            'model' => $model,
        ]);

    }

    /**
     * @return array|string
     */
    public function actionRating() {
        $this->layout = 'empty';
        $model = new ExportdataForm();
        $model->_aAllFields[] = 'msg_mark';

//        if( $model->load(Yii::$app->request->getQueryParams()) ) {
//            Yii::$app->response->format = Response::FORMAT_JSON;
//            $aValidate = ActiveForm::validate($model);
//            return $aValidate;
//        }

        if ( $model->load(Yii::$app->request->getQueryParams()) && $model->validate() ) {
            // $model->fieldslist = ['msg_id', 'msg_createtime', 'msg_subject', 'alltags', 'ekis_id', 'raitngvalue', 'fio', 'msg_pers_email', 'msg_pers_phone', 'msg_flag', 'msg_mark', 'msg_pers_text', ];
            if( empty($model->fieldslist) ) {
                $model->fieldslist = ['msg_id', 'msg_createtime', 'msg_subject', 'ratingtags', 'ekis_id', 'raitngvalue', 'fio', 'msg_pers_email', 'msg_pers_phone', 'msg_flag', 'msg_mark', ]; // 'msg_pers_text', ];
            }
            if( !isset($_REQUEST['notext']) ) {
                $model->fieldslist[] = 'msg_pers_text';
            }

            return $this->render(
                'export-rating',
                [
                    'model' => $model,
                ]
            );
        }


        return $this->renderContent($model->hasErrors() ? ('Errors: ' . print_r($model->getErrors(), true)) : 'Action need some parameters');
    }

    /**
     * Finds the Message model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Message the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        // TODO: тут нужно добавить в выборку флаги, разрешенные для пользователя, чтобы не выдавались сообщения,
        //       которые на этом уровне доступа не нужны

        if (($model = Message::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
