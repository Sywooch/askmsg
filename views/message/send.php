<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
// use app\components\ActiveForm;
// /* @var $form app\components\ActiveForm */
use yii\web\View;
use yii\helpers\Json;
use yii\web\JsExpression;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Отправить сообщение по email';

$url = \yii\helpers\Url::to(['user/find']);
$aParam = [
    'options' => ['placeholder' => 'Выбрать ...'],
    'pluginOptions' => [
        'allowClear' => true,
        'minimumInputLength' => 2,
        'ajax' => [
            'url' => $url,
            'dataType' => 'json',
            'data' => new JsExpression('function(term,page) { return {search:term}; }'),
            'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
        ],
//        'initSelection' => new JsExpression($initScript)
    ],
];
// $this->params['breadcrumbs'][] = $this->title;
$aListParam = [
//            'data' => [],
    'language' => 'ru',
    'pluginOptions' => [
        'allowClear' => true,
        'minimumInputLength' => 2,
        'ajax' =>[
            'method' => 'POST',
            'url' => "/user/find/",
            'dataType' => 'json',
            'data' => new JsExpression('function(term,page) { return { query: term, limit: 10, start: (page - 1) * 10, "_": (new Date()).getSeconds() }; }'),
            'results' => new JsExpression('function(data,page) { return {results:data.list}; }'),
/*
            'withCredentials' => false,
            'data' => new JsExpression('function (term, page) {
            console.log("term = " + term + " page = " + page);
                        return {
                            query: term,
                            limit: 10,
                            start: (page - 1) * 10,
                            "_": (new Date()).getSeconds()
                        };
                    }'),

            'results' => new JsExpression('function (data, page) {
                                console.log("results("+page+") data.total =  ["+data.total+"]");
                                var more = (page * 10) < data.total; // whether or not there are more results available
                                return {results: data.list}; // , more: more
                             }'),
            'id' => new JsExpression(
                'function(item){return item.id;}'
            ),
*/
        ],
/*
        'formatResult' => new JsExpression(
            'function (item) {
                        console.log("formatResult() item = ", item);
                        return item[val];
//                        return item.val + "<span class=\\"description\\">" + item.pos + "</span>";
                    }'
        ),
        'escapeMarkup' => new JsExpression('function (m) { console.log("escapeMarkup() m = " + m); return m; }'),
*/
    ],

    'pluginEvents' => [
        'change' => 'function(event) {
//                    var sIdReg = "'.Html::getInputId($model, 'msg_pers_region').'";
//                    jQuery("#'.Html::getInputId($model, 'msg_pers_org').'").val(event.added.text);
//                    jQuery("#"+sIdReg).val(event.added.area_id);
                    console.log("change", event);
//                    console.log("set " + sIdReg + " = " + event.added.area_id);
                }',
    ],

    'options' => [
        'placeholder' => 'Выберите из списка ...',
    ],
];

?>
<div class="site-login">
    <!-- h1><?= Html::encode($this->title) ?></h1 -->

    <?php $form = ActiveForm::begin([
        'id' => 'sendmessage-form',
        'options' => ['class' => 'form-horizontal'],
        'enableClientScript' => true, // !Yii::$app->request->isAjax,
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validateOnBlur' => false,
        'validateOnChange' => false,
        'validateOnType' => false,
        'validateOnSubmit' => true,
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-6\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-3 control-label'],
        ],
    ]);

    ?>

    <?= $form->field($model, 'id') ?>

    <?=
    $form
        ->field($model, 'uid')
        ->widget(Select2::classname(), $aParam)
//        ->widget(Select2::classname(), $aListParam)
    // $form->field($model, 'uid')
    ?>

    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-8">
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end();
    ?>

    <div class="clearfix"></div>

</div>

<?php
$sJs = <<<EOT
var oForm = jQuery('#sendmessage-form');
console.log("Add after validation function", oForm);
oForm.on('afterValidate', function (event, message) {
    console.log("After validate");
    return false;
});

oForm.on('beforeSubmit', function (event) {
    console.log("beforeSubmit");
    return false;
});


EOT;

if( Yii::$app->request->isAjax ) {
    $this->js[View::POS_READY]['aftervalidate'] = $sJs; // чтобы jquery не загружалась еще раз
}
else {
    $this->registerJs($sJs, View::POS_READY, 'aftervalidate');
}
