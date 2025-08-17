<?php
/* @var $model Recap|null */

use common\models\Recap;
use yii\helpers\Html;

?>

<div class="buttoned-header">
    <h3 title="<?= Yii::t('app', 'FRONTPAGE_WHAT_HAPPENED_TITLE_TEXT') ?>">
        <?= Yii::t('app', 'FRONTPAGE_WHAT_HAPPENED') ?>
    </h3>
    <?php if (isset($model)): ?>
        <?= Html::a(
            Yii::t('app', 'BUTTON_RECAP_VIEW_ALL'),
            ['recap/index', 'key' => $model->epic->key],
            ['class' => 'btn btn-primary']
        ); ?>
    <?php endif; ?>
</div>

<div>
    <?php if ($model) {
        if ($model->point_in_time_id) {
            echo '<p class="recap-box-time">' . $model->pointInTime . '</p>';
        }
        echo $model->getContentFormatted();
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