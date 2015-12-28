<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use app\models\Subjredirect;

/* @var $this yii\web\View */
/* @var $links array */

?>
<p><strong>Посмотрите ссылки по выбранной теме:</strong></p>
<p>
<?php

$sDop = '';
foreach($links As $ob) {
    /** @var Subjredirect $ob */
    echo $sDop . Html::a($ob->redir_description ? $ob->redir_description : $ob->redir_adress, $ob->redir_adress, ['target' => '_blank']);
    $sDop = '<br />';
}
?>
</p>
