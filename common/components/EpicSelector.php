<?php

namespace common\components;

use common\models\EpicQuery;
use common\models\User;
use yii\base\BootstrapInterface;

class EpicSelector implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     * @todo Consider issues of accessibility and availability to the player so no one substitutes key for illegal access
     */
    public function bootstrap($app)
    {
        $chosenEpicKey = isset($app->request->cookies['_epic']) ? (string)$app->request->cookies['_epic'] : null;
        $chosenEpic = EpicQuery::findOne(['key' => $chosenEpicKey]);
        $app->params['activeEpic'] = $chosenEpic;
    }
}
