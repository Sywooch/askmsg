<?php

namespace app\controllers;

use Yii;
use app\models\Message;
use app\models\MessageSearch;

use yii\db\ActiveRecord;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\Rolesimport;

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
                        'actions' => ['index', 'list', 'create', 'view'],
                        'roles' => ['?', '@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'delete', 'moderatelist'],
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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
            return $this->renderPartial('_view', [
                    'model' => $model,
                ]);
        }

        return $this->render('view', [
            'model' => $model,
        ]);
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
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
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
            $model->on(ActiveRecord::EVENT_AFTER_INSERT, [$model, 'saveCoanswers'] );
        }
        else {
            $model = $this->findModel($id);
            $model->scenario = 'moderator';
            if( $model->msg_empl_id !== null ) {
                $model->employer = $model->employee->getFullName();
            }
            // поправим после сохранения сообщения всех соответчиков
            $model->on(ActiveRecord::EVENT_AFTER_UPDATE, [$model, 'saveCoanswers'] );
        }


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
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
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
