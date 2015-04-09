<?php

namespace app\controllers;

use app\models\Msgflags;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use app\models\Rolesimport;
use app\models\Message;
use app\models\MessageSearch;

/**
 * MessageController implements the CRUD actions for Message model.
 */
class MessageController extends Controller
{
    public $defaultAction = 'create';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'list', 'create', 'view', 'export'],
                        'roles' => ['?', '@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['toword'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'delete', 'moderatelist', 'upload', 'instruction', 'testmail'],
                        'roles' => [Rolesimport::ROLE_MODERATE_DOGM],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['answerlist', 'answer'],
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

        return $this->render(
            (substr($format, 0, 3) == 'doc') ? 'export-doc' : 'export-wt',
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
        $sname = (new \ReflectionClass($searchModel))->getShortName();
        $aExcl = array('msg_empl_id');

        foreach($aExcl As $v) {
            $sField = $sname . '[' . $v . ']';
            if( isset($params[$sname]) && isset($params[$sname][$v]) ) {
                unset($params[$sname][$v]);
            }
        }

        $dataProvider = $searchModel->search($params);

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
        if( Yii::$app->request->isAjax ) {
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
        return $this->render('msgtoword', [
            'model' => $model,
        ]);

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
            throw new ForbiddenHttpException('Message is not editable');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->uploadFiles();
            return $this->redirect(['answerlist']);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
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
            // поправим после сохранения сообщения всех соответчиков
            // $model->on(ActiveRecord::EVENT_AFTER_INSERT, [$model, 'saveCoanswers'] );
        }
        else {
            $model = $this->findModel($id);
            $model->scenario = 'moderator';
            if( $model->msg_empl_id !== null ) {
                $model->employer = $model->employee->getFullName();
            }
            // поправим после сохранения сообщения всех соответчиков
            $model->on(ActiveRecord::EVENT_AFTER_UPDATE, [$model, 'saveCoanswers'] );
            $model->on(ActiveRecord::EVENT_AFTER_UPDATE, [$model, 'saveAlltags'] );
        }


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->uploadFiles();
            if( $model->scenario == 'person' ) {
                return $this->render(
                        'thankyou',
                        [
                            'model' => $model,
                        ]
                    );
            }
            else {
                return $this->redirect(['moderatelist']);
            }
        } else {
            Yii::info('MESSAGE ERROR: ' . print_r($model->getErrors(), true));
            return $this->render('create', [
                'model' => $model,
            ]);
        }
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
