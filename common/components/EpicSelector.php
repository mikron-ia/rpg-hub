<?php

namespace common\components;

use common\models\EpicQuery;
use common\models\Participant;
use common\models\User;
use yii\base\BootstrapInterface;

class EpicSelector implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        $cookies = $app->request->cookies;
        $chosenEpicKey = isset($cookies['_epic']) ? (string)$cookies['_epic'] : null;
        $chosenEpic = EpicQuery::findOne(['key' => $chosenEpicKey]);

        if($chosenEpic === null || $chosenEpic->isUserYourParticipant($app->user->identity)) {
            $app->params['activeEpic'] = $chosenEpic;
        } else {
            $app->response->cookies->remove('_epic');
            $app->params['activeEpic'] = null;
        }
    }
}
