<?php

namespace console\controllers;

use common\models\User;
use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        /* Actions */
        $controlEpic = $auth->createPermission('controlEpic');
        $controlEpic->description = 'Able to add, edit, and command an epic';
        $auth->add($controlEpic);

        /* Roles */
        $user = $auth->createRole('user'); // person responsible for handling epics
        $auth->add($user);

        $player = $auth->createRole('player');
        $auth->add($player);
        $auth->addChild($player, $user);

        $gm = $auth->createRole('GM');
        $auth->add($gm);
        $auth->addChild($gm, $user);

        $manager = $auth->createRole('manager'); // person responsible for management
        $auth->add($manager);

        $handler = $auth->createRole('handler');
        $auth->add($handler);
        $auth->addChild($handler, $manager);

        $administrator = $auth->createRole('administrator');
        $auth->add($administrator);
        $auth->addChild($administrator, $manager);
    }
}
