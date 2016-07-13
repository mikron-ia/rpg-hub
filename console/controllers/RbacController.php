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

        $controlPerson = $auth->createPermission('controlPerson');
        $controlPerson->description = 'Able to add, edit, and remove a person';
        $auth->add($controlPerson);

        $controlCharacter = $auth->createPermission('controlCharacter');
        $controlCharacter->description = 'Able to add, edit, and remove a character';
        $auth->add($controlCharacter);

        $controlStory = $auth->createPermission('controlStory');
        $controlStory->description = 'Able to add, edit, and move a story';
        $auth->add($controlStory);

        $controlRecap = $auth->createPermission('controlRecap');
        $controlRecap->description = 'Able to add, edit, and move a recap';
        $auth->add($controlRecap);

        $controlGroup = $auth->createPermission('controlGroup');
        $controlGroup->description = 'Able to add, edit, and remove a group; includes rights to add and remove members';
        $auth->add($controlGroup);

        $controlUser = $auth->createPermission('controlUser');
        $controlUser->description = 'Able to add, edit, and hide a user';
        $auth->add($controlUser);

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
