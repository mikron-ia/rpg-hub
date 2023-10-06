<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Scribble $model */

$favoriteButtonTexts = [
    false => Yii::t('app', 'SCRIBBLES_TITLE_NO'),
    true => Yii::t('app', 'SCRIBBLES_TITLE_YES'),
];

?>
<div class="scribble-view">

    <?= Html::button($favoriteButtonTexts[$model->favorite], [
        'id' => 'favorite-button',
        'class' => 'btn btn-primary btn-block',
        'data-scribble-id' => $model->scribble_id,
    ]) ?>

</div>

<script>
    let favorite = <?= (int)$model->favorite ?>;
    const isFavoriteText = "<?= $favoriteButtonTexts[true] ?>";
    const isNotFavoriteText = "<?= $favoriteButtonTexts[false]  ?>";

    $('#favorite-button').on('click', function () {
        $.ajax(
            '../scribble/reverse-favorite',
            {
                method: "GET",
                data: {
                    id: <?= $model->scribble_id ?>
                }
            }
        ).done(function () {
            favorite = !favorite;
            $(this).val(favorite ? isFavoriteText : isNotFavoriteText);
        });
    });
</script>