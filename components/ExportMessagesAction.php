<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 08.02.2016
 * Time: 14:50
 */

namespace app\components;

use yii;
use yii\base\Action;
use yii\web\Response;
use yii\widgets\ActiveForm;

use app\models\ExportdataForm;

class ExportMessagesAction extends Action {

    public function run() {
        $controller = $this->controller;

        $model = new ExportdataForm();

        if( Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) ) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $aValidate = ActiveForm::validate($model);
            return $aValidate;
        }

        if ( $model->load(Yii::$app->request->post()) && $model->validate() ) {
            return $controller->render(
                'export-data',
                [
                    'model' => $model,
                ]
            );
            return $controller->renderContent('ExportMessagesAction: attr = ' . nl2br(print_r($model->attributes, true)));
        }


        return $controller->render(
            'exportdataform',
            [
                'model' => $model,
            ]
        );
    }
}