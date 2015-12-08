<?php

namespace app\controllers;

use Yii;
use app\models\Tags;
use app\models\TagsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * TagsController implements the CRUD actions for Tags model.
 */
class TagsController extends Controller
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
     * Lists all Tags models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TagsSearch();
        $searchModel->tag_active = 1;
//        $searchModel->tag_type = Tags::TAGTYPE_TAG;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Tags for subject.
     * @return mixed
     */
    public function actionList($id = 0)
    {
        if( $id == 0 ) {
            $a = ArrayHelper::map(
                Tags::getTagslist(Tags::TAGTYPE_TAG),
                'tag_id',
                'tag_title'
            );
        }
        else {
            $a = ArrayHelper::map(
                Tags::getTagslist(Tags::TAGTYPE_TAG, $id),
                'tag_id',
                'tag_title'
            );
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $a;
    }

    /**
     * Displays a single Tags model.
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
     * Creates a new Tags model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param integer $type
     * @return mixed
     */
    public function actionCreate($type = 0)
    {
        $type = intval($type);

        $model = new Tags();
        if( in_array($type, array_keys(Tags::$_aTypes))  ) {
            $model->tag_type = $type;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
//            return $this->redirect(['view', 'id' => $model->tag_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Tags model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
//            return $this->redirect(['view', 'id' => $model->tag_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Tags model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        /** @var  $model Tags */
        $model = $this->findModel($id); // ->delete();
        $model->tag_active = $model->tag_active == 1 ? 0 : 1;
        $model->save();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Tags model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tags the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tags::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
