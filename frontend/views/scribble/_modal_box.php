<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Scribble $model */

$favoriteButtonTexts = [
    false => Yii::t('app', 'SCRIBBLES_BUTTON_NO'),
    true => Yii::t('app', 'SCRIBBLES_BUTTON_YES'),
];

?>
<div class="scribble-view">
    <p id="scribble-modal-error-box" class="error-summary"></p>
    <?= Html::button($favoriteButtonTexts[$model->favorite], [
        'id' => 'favorite-button',
        'class' => 'btn btn-primary btn-block',
        'data-scribble-id' => $model->scribble_id,
    ]) ?>
</div>

<script>
    var favorite = <?= (int)$model->favorite ?>;

    $('#scribble-modal-error-box').hide();

    $('#favorite-button').on('click', function () {
        const button = $('#favorite-button');
        button.text('<?= Yii::t('app', 'SCRIBBLES_BUTTON_WORKING') ?>');
        button.prop('disabled', true);

        $.ajax(
            '../scribble/reverse-favorite',
            {
                method: 'GET',
                data: {
                    id: $(this).data('scribble-id')
                }
            }
        ).done(function () {
            favorite = !favorite;
            $('#scribble-modal-error-box').hide();
        }).fail(function () {
            const errorBox = $('#scribble-modal-error-box');
            errorBox.text('<?= Yii::t('app', 'SCRIBBLES_FAVORITE_ERROR_GENERIC') ?>');
            errorBox.show();
        }).always(function () {
            button.prop('disabled', false);
            $('#favorite-button').text(favorite ? "<?= $favoriteButtonTexts[true] ?>" : "<?= $favoriteButtonTexts[false]  ?>");
        });
    });
</script>
