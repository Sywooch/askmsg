<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

use app\models\User;
use app\models\UserSearch;
use app\models\Group;
use app\models\Rolesimport;
use app\models\Usergroup;
use yii\db\Query;
/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['find', ],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['answerlist', ],
                        'roles' => [Rolesimport::ROLE_MODERATE_DOGM],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'update', 'view'],
                        'roles' => [Rolesimport::ROLE_MODERATE_DOGM],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete', ],
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
     * Lists all Answer users.
     * @return mixed
     */
    public function actionAnswerlist()
    {
        $sQuery = Yii::$app->request->get('query');
        $a = explode(' ', $sQuery);
        if( count($a) > 1 ) {
            $sQuery = $a[0];
        }

        $aData = ArrayHelper::map(
            User::find()
                ->select(User::tableName() . '.*, ' . Usergroup::tableName() . '.*')
                ->innerJoin(Usergroup::tableName(), 'us_id = usgr_uid')
                ->where([
                    'and',
//                    ['or', ['like', 'us_lastname', $sQuery], ['like', 'us_name', $sQuery], ['like', 'us_secondname', $sQuery]],
                    ['like', 'us_lastname', $sQuery],
                    ['usgr_gid' => Rolesimport::ROLE_ANSWER_DOGM],
                ])
                ->orderBy(['us_lastname' => SORT_ASC, 'us_name' => SORT_ASC, 'us_secondname' => SORT_ASC])
                ->all(),
            'us_id',
            function($ob) {
                return [
                    'id' => $ob->us_id,
                    'val' => $ob->getFullName(),
                    'pos' => $ob->us_workposition,
                ];
            }
        );

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $aData;
    }

    /**
     * Lists all users.
     * @return mixed
     */
    public function actionFind($search = null, $id = null)
    {
        $out = ['more' => false];
        if (!is_null($search)) {
            $sQuery = $search;
//            $query = new Query;
            $query = User::find();
            $query->select('us_id, us_lastname AS text')
//                ->from('city')
                ->where(['or',
                        ['like', 'us_lastname', $sQuery],
                        ['like', 'us_name', $sQuery],
                        ['like', 'us_secondname', $sQuery],
                        ['like', 'us_workposition', $sQuery],
                    ]
                );
            $query1 = clone $query;
            $out['more'] = $query1->count();
//                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => City::find($id)->name];
        }
        else {
            $out['results'] = ['id' => 0, 'text' => 'No matching records found'];
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//        echo Json::encode($out);
        return $out;

        $sQuery = Yii::$app->request->get('query', '');
        $nLimit = Yii::$app->request->get('limit', 10);
        $nStart = Yii::$app->request->get('start', 0);
        $a = explode(' ', $sQuery);
        if( count($a) > 1 ) {
            $sQuery = $a[0];
        }

        $oQuery = User::find()
            ->select(User::tableName() . '.* ')
            ->where(//[
//                    'and',
                ['or',
                    ['like', 'us_lastname', $sQuery],
                    ['like', 'us_name', $sQuery],
                    ['like', 'us_secondname', $sQuery],
                    ['like', 'us_workposition', $sQuery],
                ]
//                    ['like', 'us_lastname', $sQuery],
            //]
            )
            ->orderBy(['us_lastname' => SORT_ASC, 'us_name' => SORT_ASC, 'us_secondname' => SORT_ASC]);

        $oQuery2 = clone $oQuery;
        $nAll = $oQuery2->count();
        $aData = ArrayHelper::map(
            $oQuery
                ->limit($nLimit)
                ->offset($nStart)
                ->all(),
            'us_id',
            function($ob) {
                return $ob->getFullName();
                return [
                    'id' => $ob->us_id,
                    'val' => $ob->getFullName(),
                    'pos' => $ob->us_workposition,
                ];
            }
        );

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'total' => $nAll,
            'list' => $aData,
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $model->us_active = User::STATUS_ACTIVE;
        $model->scenario = 'create';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->unlinkAll('permissions', true);
            foreach($model->selectedGroups As $gid) {
                $oGroup = Group::getGroupById($gid);
                if( $oGroup !== null ) {
                    $model->link('permissions', $oGroup);
//                    Yii::info('Add group ' . $gid);
                }
            }
            return $this->redirect(['index']);
//            return $this->redirect(['view', 'id' => $model->us_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';
        $model->getArrayGroups();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->unlinkAll('permissions', true);
            foreach($model->selectedGroups As $gid) {
                $oGroup = Group::getGroupById($gid);
                if( $oGroup !== null ) {
                    $model->link('permissions', $oGroup);
//                    Yii::info('Add group ' . $gid);
                }
//                else {
//                    Yii::info("No group: {$gid}");
//                }
            }
//            return $this->redirect(['view', 'id' => $model->us_id]);
            return $this->redirect(['index']);
        }

        return $this->render(
            'update',
            [
                'model' => $model,
            ]
        );
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
//        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
