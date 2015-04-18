<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;

$this->title = ($exception->statusCode == 403 || $exception->statusCode == 404) ? 'Страница не найдена' : 'Ошибка на сайте';

Yii::error("ERROR PAGE: {$exception->statusCode} :\n{$name}\n{$message}\n" . print_r($exception, true));

// The above error occurred while the Web server was processing your request.
// Please contact us if you think this is a server error. Thank you.
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= '' //nl2br(Html::encode($message)) ?>
    </div>

    <p>
        Произошла непредвиденная ошибка. Мы уже работаем над ее устранением.
    </p>
    <p>
        Для продолжения работы перейдите <a href="/">на главную страницу</a>.
    </p>

</div>
