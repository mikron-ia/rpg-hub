<?php

use common\components\processor\MarkdownProcessor;
use common\models\UserInvitation;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $invitation UserInvitation */
/* @var $link string */

?>
<div class="invitation">
    <p><?= Yii::t('mail', 'GREETING') ?>,</p>

    <div>
        <?= MarkdownProcessor::process($invitation->message) ?>
    </div>

    <p>
        <?= sprintf(
            '%s: %s',
            Yii::t('mail', 'USER_INVITATION_BODY_TEXT'),
            Html::a(Html::encode($link), $link)
        );
        ?>
    </p>

    <p>
        <?= Yii::t(
            'mail',
            'USER_INVITATION_EXPIRATION_WARNING {when}',
            ['when' => date('Y-m-d H:i:s', $invitation->valid_to)]
        ) ?>
    </p>
</div>
