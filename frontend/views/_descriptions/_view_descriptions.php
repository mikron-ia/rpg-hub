<?php
/* @var $this yii\web\View */

use common\models\core\HasDescriptions;
use common\models\Description;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;

/* @var $model HasDescriptions */
/* @var $showPrivates bool */

$descriptions = $model->getDescriptionsVisible();
?>

<?php if ($descriptions): ?>
    <div id="descriptions">
        <?= ListView::widget([
            'dataProvider' => new ActiveDataProvider([
                'query' => $descriptions,
                'sort' => ['defaultOrder' => ['position' => SORT_ASC]]
            ]),
            'itemOptions' => ['class' => 'item'],
            'summary' => '',
            'itemView' => function (Description $model, $key, $index, $widget) {
                return $this->render(
                    '_view_description',
                    [
                        'model' => $model,
                        'key' => $key,
                        'index' => $index,
                        'widget' => $widget,
                        'showPrivates' => $this->params['showPrivates']
                    ]
                );
            },
        ]) ?>
    </div>
<?php else: ?>
    <p><?= Yii::t('app', 'DESCRIPTIONS_NOT_FOUND'); ?></p>
<?php endif; ?>
