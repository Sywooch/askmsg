<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\web\UploadedFile;

use app\models\Appeal;
use app\models\AppealSearch;
use app\components\AppealActions;

/**
 * AppealController implements the CRUD actions for Appeal model.
 */
class AppealController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'view', 'captcha', ],
                        'roles' => ['?', '@'],
                    ],
//                    [
//                        'allow' => true,
//                        'actions' => ['toword', 'send', 'curatortest'],
//                        'roles' => ['@'],
//                    ],
//                    [
//                        'allow' => true,
//                        'actions' => ['update', 'delete', 'moderatelist', 'upload', 'instruction', 'testmail', 'exportdata'],
//                        'roles' => [Rolesimport::ROLE_MODERATE_DOGM],
//                    ],
//                    [
//                        'allow' => true,
//                        'actions' => ['answerlist', 'answer'],
//                        'roles' => [Rolesimport::ROLE_ANSWER_DOGM],
//                    ],
//                    [
//                        'allow' => true,
//                        'actions' => ['admin'],
//                        'roles' => [Rolesimport::ROLE_ADMIN],
//                    ],
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
     * Lists all Message models for users.
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new AppealSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Appeal models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AppealSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index-public', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Appeal model.
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
     * Creates a new Appeal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->actionUpdate(0);
//        $model = new Appeal();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->ap_id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
    }

    /**
     * Updates an existing Appeal model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if( $id == 0 ) {
            $model = new Appeal();
            $nSubjectId = Yii::$app->request->getQueryParam('subid', null);
            if( $nSubjectId == 254 ) {
                $model->ap_subject = $nSubjectId;
                $model->ap_pers_text = "Здравствуйте,\n\nЯ хотел бы задать вопрос по заработной плате педагогов:\n\n";
            }
        }
        else {
            $model = $this->findModel($id);
        }

        if( Yii::$app->request->isAjax ) {
            if ($model->load(Yii::$app->request->post())) {
                $aValidate = ActiveForm::validate($model);
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $aValidate;
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $oAct = new AppealActions($model);
            $oAct->addFilesToAppeal(UploadedFile::getInstances($model, 'file'));
            return $this->render(
                'thankyou',
                [
                    'model' => $model,
                ]
            );

//            return $this->redirect(['view', 'id' => $model->ap_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Appeal model.
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
     * Finds the Appeal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Appeal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Appeal::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
