<?php

/** @var $model \common\models\Character */
/** @var array $data */

$content = [];

foreach($data['data'] as $row) {
    $result = $data['rowTemplate'];


}

$body =

$table = str_replace('[caption]', '<caption>' . $title . '</caption>', $data['tableTemplate']);
$table = str_replace('[body]', '', $table);
$table = str_replace('[header]', '', $table);
$table = str_replace('[footer]', '', $table);

?>

<div>

    <?= $table ?>

</div>
