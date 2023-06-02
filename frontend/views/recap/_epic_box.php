<?php
/* @var $model Recap */

use common\models\Recap;
use yii\helpers\Html;

?>

<div class="buttoned-header">
    <h3 title="<?= Yii::t('app', 'FRONTPAGE_WHAT_HAPPENED_TITLE_TEXT') ?>">
        <?= Yii::t('app', 'FRONTPAGE_WHAT_HAPPENED') ?>
    </h3>
    <?= Html::a(
        Yii::t('app', 'BUTTON_RECAP_VIEW_ALL'),
        ['recap/index'],
        ['class' => 'btn btn-primary']
    ); ?>
</div>

<div>
    <?php if ($model) {
        if ($model->point_in_time_id) {
            echo '<p class="recap-box-time">' . $model->pointInTime . '</p>';
        }
        echo $model->getDataFormatted();
        if (!empty($model->games)) {
            echo '<p>'
                . '<strong>' . Yii::t('app', 'LABEL_GAMES') . ': </strong>'
                . $model->getSessionNamesFormatted()
                . '</p>';
        }
    } else {
        echo '<p class="error-box">' . Yii::t('app', 'FRONTPAGE_RECAP_NOT_AVAILABLE') . '</p>';
    } ?>
</div>