<?php

use common\models\Group;

/* @var $this yii\web\View */
/* @var $header string */
/* @var $model Group */
/* @var $storyGroupPublic array<string> */
/* @var $storyGroupPrivate array<string> */
/* @var $showPrivateWarning bool */

?>

<?php if (!empty($storyGroupPublic)): ?>
    <div class="col-md-12">
        <?php if ($showPrivateWarning): ?>
            <h2 class="text-center"><?= Yii::t('app', 'STORY_ASSIGNMENT_STORY_PUBLIC'); ?></h2>
            <p class="warning-box"><?= Yii::t('app', 'STORY_ASSIGNMENT_STORY_PRIVATE_WARNING'); ?></p>
        <?php else: ?>
            <h2 class="text-center"><?= Yii::t('app', 'STORY_ASSIGNMENT_STORY'); ?></h2>
        <?php endif; ?>
        <ul>
            <?php foreach ($storyGroupPublic as $role): ?>
                <li><?= $role; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <p class="info-box"><?= Yii::t('app', 'STORY_ASSIGNMENT_GROUP_EMPTY'); ?></p>
<?php endif; ?>


<?php if (!empty($storyGroupPrivate)): ?>
    <div class="col-md-12">
        <h2 class="text-center"><?= Yii::t('app', 'STORY_ASSIGNMENT_STORY_PRIVATE'); ?></h2>
        <ul>
            <?php foreach ($storyGroupPrivate as $role): ?>
                <li><?= $role; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

