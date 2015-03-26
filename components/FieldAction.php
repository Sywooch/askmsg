<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 26.03.2015
 * Time: 18:08
 */

namespace app\components;

use yii\rest\Action;


class FieldAction extends Action {

    public $defaultfield = 'id';

    /**
     * Возвращает значение поля
     * @param string $id the primary key of the model.
     * @param string $name field name
     * @return array
     */
    public function run($id, $name = '')
    {
        $model = $this->findModel($id);
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        if( $name == '' ) {
            $name = $this->defaultfield;
        }
        if( isset($model->attributes[$name]) ) {
            $data = [
                $name => $model->attributes[$name]
            ];
        }
        else {
            $data = [];
        }

        return $data;
    }
}