<?php

use common\dto\LinkWithVisibility;
use common\models\Group;
use yii\web\View;

/* @var $this View */
/* @var $header string */
/* @var $model Group */
/* @var $storyGroupPublic array<LinkWithVisibility> */
/* @var $storyGroupPrivate array<LinkWithVisibility> */
/* @var $showPrivateWarning bool */
?>

<?php if (!empty($storyGroupPublic)): ?>
    <div class="col-md-6">
        <?php if ($showPrivateWarning): ?>
            <h2 class="text-center"><?= Yii::t('app', 'STORY_ASSIGNMENT_STORY_PUBLIC'); ?></h2>
        <?php else: ?>
            <h2 class="text-center"><?= Yii::t('app', 'STORY_ASSIGNMENT_STORY'); ?></h2>
        <?php endif; ?>
        <ul>
            <?php foreach ($storyGroupPublic as $role): ?>
                <li class="<?= $role->isSecret ? 'secret list-item-hidden' : '' ?>"><?= $role; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <p class="info-box"><?= Yii::t('app', 'STORY_ASSIGNMENT_GROUP_EMPTY'); ?></p>
<?php endif; ?>


<?php if (!empty($storyGroupPrivate)): ?>
    <div class="col-md-6">
        <h2 class="text-center"><?= Yii::t('app', 'STORY_ASSIGNMENT_STORY_PRIVATE'); ?></h2>
        <ul>
            <?php foreach ($storyGroupPrivate as $role): ?>
                <li class="<?= $role->isSecret ? 'secret list-item-hidden' : '' ?>"><?= $role; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
