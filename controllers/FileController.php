<?php

namespace app\controllers;

use Yii;
use app\models\File;
use app\models\FileSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\Rolesimport;

/**
 * FileController implements the CRUD actions for File model.
 */
class FileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [

                    [
                        'allow' => true,
                        'actions' => ['download', 'tdir', 'getfile'],
                        'roles' => ['?', '@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['remove'],
                        'roles' => [Rolesimport::ROLE_MODERATE_DOGM, Rolesimport::ROLE_ANSWER_DOGM],
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
     * Display save as dialof for all files
     * @param string $name
     * @return mixed
     */
    public function actionDownload($name = '')
    {
        Yii::info("actionDownload({$name})");
        /** @var File $model */
        $model = File::find()->where(['file_name' => $name])->one();
        Yii::info(print_r($model->attributes, true));
        if( ($name == '') || ($model === null) ) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        Yii::$app->response->sendFile($model->getFullpath(), $model->file_orig_name);
    }

    /**
     * Lists all File models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FileSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all File models.
     * @return mixed
     */
    public function actionUpload()
    {
    }

    /**
     * Remove file model
     * @param integer $id
     * @return mixed
     */
    public function actionRemove($id)
    {
        $model = $this->findModel($id);
        $sf = $model->getFullpath();
        if( file_exists($sf) ) {
            unlink($sf);
        }
        $model->delete();
        $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Displays a single File model.
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
     * Creates a new File model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new File();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->file_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing File model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->file_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing File model.
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
     * @return string
     */
    public function actionTdir()
    {
        $sDir = str_replace('/', DIRECTORY_SEPARATOR, Yii::getAlias(Yii::$app->params['message.file.uploaddir']));
        $s = $this->testdirfile($sDir, '|^[\w\-\.]+$|');
        return $this->renderContent(nl2br("Result: \n" . $s));
    }

    /**
     * @return string
     */
    public function actionGetfile($dname, $fname)
    {
        $sDir = str_replace(['/', '\''], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], Yii::getAlias(Yii::$app->params['message.file.uploaddir']));
        $sf = $sDir . DIRECTORY_SEPARATOR . $dname . DIRECTORY_SEPARATOR . $fname;

//        return $this->renderContent('actionGetfile('.$dname.', '.$fname.') : ' . $sf . ' ' . (file_exists($sf) ? '' : 'not ') . ' exists');

        if( Yii::$app->user->isGuest || !file_exists($sf) ) {
            throw new NotFoundHttpException('Ошибка загрузки страницы.');
        }

        Yii::$app->response->sendFile($sf);

//        return $this->renderContent(nl2br("Result: \n" . $s));
    }

    /**
     * @param $sDir
     * @param $sRegexp
     * @return string
     */
    public function testdirfile($sDir, $sRegexp) {
        $sRes = '';
        if( $hd = opendir($sDir) ) {
            $nCou = 0;
            while( false !== ($s = readdir($hd)) ) {
                if( trim($s, '.') == '' ) {
                    continue;
                }
                $sf = $sDir . DIRECTORY_SEPARATOR . $s;
                $nCou++;
                if( is_dir($sf) ) {
                    $sRes .= $this->testdirfile($sf, $sRegexp);
                }
                else {
                    if( !preg_match($sRegexp, $s) ) {
                        $sRes .= $sf . "\n";
                    }
                }
            }
            $sRes .= $sDir . ': ' . $nCou . " files\n";
            closedir($hd);
        }
        return $sRes;
    }

    /**
     * Finds the File model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return File the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = File::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
