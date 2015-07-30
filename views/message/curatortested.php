<?php

use yii\helpers\Html;
use app\models\Msgflags;

/* @var $this yii\web\View */
/* @var $model app\models\Message */
?>
<div class="alert alert-success" role="alert">

    <h3>Сообщение проверено</h3>
    <?php
        if( in_array($model->msg_flag, [Msgflags::MFLG_INT_REVIS_INSTR, Msgflags::MFLG_SHOW_REVIS]) ) {
    ?>
        <p>Ответ на обращение отправлен на доработку исполнителю <?= $model->employer ?></p>
    <?php
        }
        else {
    ?>
        <p>Ответ согласован и отправлен на утверждение модератору.</p>
    <?php
        }
    ?>

</div>
