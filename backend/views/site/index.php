<?php

/* @var $this yii\web\View */

$this->title = 'RPG hub - control';
?>
<div class="site-index">

    <div class="jumbotron">

        <h1>RPG Hub</h1>

        <p class="lead">Data hub for the campaign is operational</p>

        <p><a class="btn btn-lg btn-success" href="index.php/story/">List stories</a></p>

    </div>

    <div class="body-content">

        <div class="row">

            <div class="col-lg-4">
                <h2><?php echo Yii::t('app', 'RECAP_TITLE_INDEX'); ?></h2>

                <p>Recaps describe past of the party.</p>

                <p><a class="btn btn-default" href="index.php/recap/">List recaps &raquo;</a></p>
            </div>

        </div>

    </div>
</div>
