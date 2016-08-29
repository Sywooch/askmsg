<?php

namespace app\controllers;

use Yii;
use app\models\SubjectTree;
use app\models\SubjectTreeSearch;
use yii\base\Model;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\Subjecttreeimport;
use app\models\MessageTreeForm;
use yii\widgets\ActiveForm;
use yii\web\Response;

/**
 * SubjecttreeController implements the CRUD actions for SubjectTree model.
 */
class SubjecttreeController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all SubjectTree models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SubjectTreeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SubjectTree model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id = 0)
    {
        try {
            $model = $this->findModel($id);
        }
        catch(NotFoundHttpException $e) {
            $model = null;
            $id = 0;
        }

        $formmodel = new MessageTreeForm();

        return $this->render('treelevel', [
            'model' => $model,
            'formmodel' => $formmodel,
            'child' => ($model !== null) ? $this->findChild($model) : [],
            'parents' => $this->findParents($model),
        ]);
    }

    /**
     * Импорт данных из файлов
     * @return mixed
     */
    public function actionImport()
    {

        $ob = new Subjecttreeimport();
        $sDir = Yii::getAlias('@app/runtime/subject');
        $sDir = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $sDir);

        $ob->importDir($sDir);
        return $this->renderContent($sDir);

    }

    /**
     * Creates a new SubjectTree model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SubjectTree();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->subj_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @return mixed
     */
    public function actionNewmsg($id = 0)
    {
        try {
            $model = $this->findModel($id);
        }
        catch(NotFoundHttpException $e) {
            $model = null;
            $id = 0;
        }

        $formmodel = new MessageTreeForm();
        $formmodel->scenario = 'newmsg';

        if( Yii::$app->request->isAjax && $formmodel->load(Yii::$app->request->post()) ) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if( $model === null ) {
                $formmodel->on(
                    Model::EVENT_AFTER_VALIDATE,
                    function($event) {
                        /** @var yii\base\Event $event */
                        $event->sender->addError('msg_pers_text', 'Не выбрана тема обращения');
                    }
                );
            }
            $aErr = ActiveForm::validate($formmodel);

            return $aErr;
        }

        if( $model === null ) {
            return $this->redirect(['view']);
        }

        if( $formmodel->load(Yii::$app->request->post()) && $formmodel->validate() ) {
            return $this->redirect(['view',]);
        }

        $nSatisfy = Yii::$app->request->get('satisfy', 0);
        if( $nSatisfy != 0 ) {
            $formmodel->is_satisfied = $nSatisfy;
        }

        return $this->render(
            '_formmessage',
            [
                'model' => $model,
                'formmodel' => $formmodel,
            ]
        );

    }

    /**
     * @return mixed
     */
    public function actionStepmsg()
    {
//        $model = $this->findModel($id);
        $formmodel = new MessageTreeForm();
//        $nStep = 1;
        $nStep = Yii::$app->request->post('step', 1);
        $formmodel->scenario = 'step_' . $nStep;
        Yii::info('step = ' . $formmodel->scenario);

        if( Yii::$app->request->isAjax && $formmodel->load(Yii::$app->request->post()) ) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $aErr = ActiveForm::validate($formmodel);
            Yii::info('ajax err = ' . print_r($aErr, true));
            return $aErr;
        }

        $model = null;
        $nSubjectId = 0;
        if( $formmodel->load(Yii::$app->request->post()) && $formmodel->validate() ) {
            if( $nStep >= 2 ) {
                Yii::info('formmodel->attributes = ' . print_r($formmodel->attributes, true));
                $nSubjectId = $formmodel->subject_id;
                $model = $nSubjectId ? $this->findModel($nSubjectId): null;
                $this->findChild($model);
                if( $formmodel->is_satisfied == 1 ) {
                    return $this->render(
                        'information-ok',
                        []
                    );
                }
                if( (intval($formmodel->is_user_variant) > 0) ) {
                    $nStep++;
                }
//                if( !empty($formmodel->is_satisfied) ) {
                else if( !$formmodel->isNeedSelectChild($model)
                    && !$formmodel->isNeedSatisfy($model)
                    && !$formmodel->isNeedAskdirector($model) ) {
                    $nStep++;
                }
            }
            else if( isset($_POST['next']) ) {
                $nStep++;
            }
            else if( isset($_POST['prev']) && ($nStep > 1) ) {
                $nStep--;
            }
            $formmodel->scenario = 'step_' . $nStep;

            if( $nStep > 3 ) {
                return $this->render('thankyou', []);
            }
//            return $this->redirect(['view', 'id' => $model->subj_id]);
        }
//        Yii::info('attributes = ' . print_r($formmodel->attributes, true));
        Yii::info('step = ' . $formmodel->scenario);

        return $this->render('_formmessage_v2', [
            'formmodel' => $formmodel,
            'model' => $model,
            'step' => $nStep,
            'subjectid' => $nSubjectId,
            'child' => ($nStep == 2 ) ? $this->findChild($model) : [],
            'parents' => ($nStep == 2) ? $this->findParents($model) : [],
        ]);
    }

    /**
     * Updates an existing SubjectTree model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->subj_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing SubjectTree model.
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
     * Finds the SubjectTree model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SubjectTree the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SubjectTree::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param SubjectTree $model
     * @return array of SubjectTree the loaded models
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findChild($model)
    {
        $a = ($model === null) ? SubjectTree::getChildNodes(0) : $model->getChild();
//        echo 'result = ' . print_r($a, true);
//        echo 'model = ' . print_r($model, true);
//        die();
        return $a;
    }

    /**
     * @param SubjectTree $model
     * @return array of SubjectTree the loaded models
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findParents($model)
    {
        return $model === null ? [] : $model->getParents();
    }
}
