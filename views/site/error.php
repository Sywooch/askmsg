<?php

use yii\helpers\Html;
use app\models\Message;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;

$this->title = ($exception->statusCode == 403 || $exception->statusCode == 404) ? 'Страница не найдена' : 'Ошибка на сайте';

$sMsg = 'Произошла непредвиденная ошибка. Мы уже работаем над ее устранением.';
$code = 0;
if( $exception instanceof HttpException ) {
//    echo 'exception instanceof HttpException<br />';
    $code = $exception->statusCode;
}
else {
//    echo 'exception NOT instanceof HttpException<br />';
    $code = $exception->getCode();
}

if( $code == Message::EXCAPTION_CODE_MSG_ON_MODARATE ) {
    $sMsg = 'Сообщение находится на модерации. У Вас нет возможности изменять его.';
}
elseif( $code == Message::EXCAPTION_CODE_MSG_ON_SOGL ) {
    $sMsg = 'Сообщение находится на согласовании. У Вас нет возможности изменять его.';
}

//echo nl2br(print_r($exception, true));

Yii::error("ERROR PAGE: {$exception->statusCode} {$_SERVER['REQUEST_URI']}:\n{$name}\n{$message}\n"/* . print_r($exception, true)*/);
// /bitrix/tools/public_session.php

// The above error occurred while the Web server was processing your request.
// Please contact us if you think this is a server error. Thank you.
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- div class="alert alert-danger">
        <?= '' //nl2br(Html::encode($message)) ?>
    </div -->

    <p>
        <?php echo Html::encode($sMsg); ?>
    </p>
    <p>
        Для продолжения работы перейдите <a href="/">на главную страницу</a>.
    </p>

</div>
