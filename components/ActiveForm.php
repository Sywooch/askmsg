<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 10.04.2015
 * Time: 10:13
 */

namespace app\components;

use yii\base\InvalidCallException;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\widgets\ActiveFormAsset;
use yii\helpers\Url;

class ActiveForm extends \yii\bootstrap\ActiveForm {
    public $enableFormScript = true;
    /**
     * Runs the widget.
     * This registers the necessary javascript code and renders the form close tag.
     * @throws InvalidCallException if `beginField()` and `endField()` calls are not matching
     */
    public function run()
    {
        if (!empty($this->_fields)) {
            throw new InvalidCallException('Each beginField() should have a matching endField() call.');
        }

        $view = $this->getView();
        if( $this->enableClientScript ) {
            ActiveFormAsset::register($view);
        }

        if( $this->enableClientScript || $this->enableFormScript ) {
            $id = $this->options['id'];
            $options = Json::encode($this->getClientOptions());
            $attributes = Json::encode($this->attributes);
            $view->registerJs("jQuery('#$id').yiiActiveForm($attributes, $options);");
        }

        echo Html::endForm();
    }

    /**
     * Returns the options for the form JS widget.
     * @return array the options
     */
    protected function getClientOptions()
    {
        $options = [
            'encodeErrorSummary' => $this->encodeErrorSummary,
            'errorSummary' => '.' . implode('.', preg_split('/\s+/', $this->errorSummaryCssClass, -1, PREG_SPLIT_NO_EMPTY)),
            'validateOnSubmit' => $this->validateOnSubmit,
            'errorCssClass' => $this->errorCssClass,
            'successCssClass' => $this->successCssClass,
            'validatingCssClass' => $this->validatingCssClass,
            'ajaxParam' => $this->ajaxParam,
            'ajaxDataType' => $this->ajaxDataType,
        ];
        if ($this->validationUrl !== null) {
            $options['validationUrl'] = Url::to($this->validationUrl);
        }

        // only get the options that are different from the default ones (set in yii.activeForm.js)
        return array_diff_assoc($options, [
            'encodeErrorSummary' => true,
            'errorSummary' => '.error-summary',
            'validateOnSubmit' => true,
            'errorCssClass' => 'has-error',
            'successCssClass' => 'has-success',
            'validatingCssClass' => 'validating',
            'ajaxParam' => 'ajax',
            'ajaxDataType' => 'json',
        ]);
    }

}