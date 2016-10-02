<?php

namespace console\controllers;

use common\models\User;
use common\rules\EpicAssistant;
use common\rules\EpicGameMaster;
use common\rules\EpicParticipant;
use common\rules\EpicPlayer;
use common\rules\EpicWatcher;
use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        /* Rules */
        $participantRule = new EpicParticipant();
        $watcherRule = new EpicWatcher();
        $playerRule = new EpicPlayer();
        $gameMasterRule = new EpicGameMaster();
        $assistantRule = new EpicAssistant();

        $auth->add($participantRule);
        $auth->add($watcherRule);
        $auth->add($playerRule);
        $auth->add($gameMasterRule);
        $auth->add($assistantRule);

        /* Epic-specific actions */

        $openEpic = $auth->createPermission('openEpic');
        $openEpic->description = 'Able to add and open an epic';
        $auth->add($openEpic);

        $controlEpic = $auth->createPermission('controlEpic');
        $controlEpic->description = 'Able to edit, and command an epic';
        $controlEpic->ruleName = $gameMasterRule->name;
        $auth->add($controlEpic);

        $controlPerson = $auth->createPermission('controlPerson');
        $controlPerson->description = 'Able to add, edit, and remove a person';
        $controlPerson->ruleName = $gameMasterRule->name;
        $auth->add($controlPerson);

        $controlCharacter = $auth->createPermission('controlCharacter');
        $controlCharacter->description = 'Able to add, edit, and remove a character';
        $controlCharacter->ruleName = $gameMasterRule->name;
        $auth->add($controlCharacter);

        $controlStory = $auth->createPermission('controlStory');
        $controlStory->description = 'Able to add, edit, and move a story';
        $controlStory->ruleName = $gameMasterRule->name;
        $auth->add($controlStory);

        $controlRecap = $auth->createPermission('controlRecap');
        $controlRecap->description = 'Able to add, edit, and move a recap';
        $controlRecap->ruleName = $gameMasterRule->name;
        $auth->add($controlRecap);

        $controlGroup = $auth->createPermission('controlGroup');
        $controlGroup->description = 'Able to add, edit, and remove a group; includes rights to add and remove members';
        $controlGroup->ruleName = $gameMasterRule->name;
        $auth->add($controlGroup);

        $controlSession = $auth->createPermission('controlSession');
        $controlSession->description = 'Able to announce session and control its execution';
        $controlSession->ruleName = $assistantRule->name;
        $auth->add($controlSession);

        /* General actions */

        $controlUser = $auth->createPermission('controlUser');
        $controlUser->description = 'Able to add, edit, and manage a user';
        $auth->add($controlUser);

        $controlManager = $auth->createPermission('controlManager');
        $controlManager->description = 'Able to add, edit, and manage a manager';
        $auth->add($controlManager);

        /* Roles */
        $user = $auth->createRole('user'); // basic user
        $auth->add($user);

        /*
        $player = $auth->createRole('player'); // person able to see epics they play in
        $auth->add($player);
        $auth->addChild($player, $user);
        */

        $gm = $auth->createRole('gm'); // person able to handle epics
        $auth->add($gm);
        $auth->addChild($gm, $user);
        $auth->addChild($gm, $openEpic);
        $auth->addChild($gm, $controlEpic);
        $auth->addChild($gm, $controlPerson);
        $auth->addChild($gm, $controlCharacter);
        $auth->addChild($gm, $controlStory);
        $auth->addChild($gm, $controlRecap);
        $auth->addChild($gm, $controlGroup);

        $manager = $auth->createRole('manager'); // person who handles the users and general settings
        $auth->add($manager);
        $auth->addChild($manager, $controlUser);

        $administrator = $auth->createRole('administrator'); // person who handles the users, general settings, and managers
        $auth->add($administrator);
        $auth->addChild($administrator, $controlManager);
        $auth->addChild($administrator, $manager);
    }
}
