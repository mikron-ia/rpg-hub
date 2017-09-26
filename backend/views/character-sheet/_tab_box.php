<?php

/** @var $model \common\models\CharacterSheet */

?>

<div class="col-md-6">
    <?php

    foreach ($data as $title => $tableBox) {
        echo $this->render(
            '_table_box',
            [
                'model' => $model,
                'title' => $title,
                'data' => $tableBox,
                'showPrivates' => $this->params['showPrivates']
            ]
        );
    }

    ?>

</div>
