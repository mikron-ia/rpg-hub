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

        /**
         * Minor technical note: if there is no separate index* right, all view* rights should allow listing,
         * of course not necessarily complete.
         */

        $openEpic = $auth->createPermission('openEpic');
        $openEpic->description = 'Able to add and open an epic';
        $auth->add($openEpic);

        $controlEpic = $auth->createPermission('controlEpic');
        $controlEpic->description = 'Able to edit and command an epic';
        $controlEpic->ruleName = $gameMasterRule->name;
        $auth->add($controlEpic);

        $viewEpic = $auth->createPermission('viewEpic');
        $viewEpic->description = 'Able to view an epic';
        $viewEpic->ruleName = $playerRule->name;
        $auth->add($viewEpic);

        $controlPerson = $auth->createPermission('controlPerson');
        $controlPerson->description = 'Able to add, edit, and remove a person';
        $controlPerson->ruleName = $gameMasterRule->name;
        $auth->add($controlPerson);

        $viewPerson = $auth->createPermission('viewPerson');
        $viewPerson->description = 'Able to view a person';
        $viewPerson->ruleName = $playerRule->name;
        $auth->add($viewPerson);

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
        $user = $auth->createRole('user'); // this is the basic user, confined to the front
        $auth->add($user);
        $auth->addChild($user, $viewEpic);
        $auth->addChild($user, $viewPerson);
        $auth->addChild($user, $viewCharacter);
        $auth->addChild($user, $viewStory);
        $auth->addChild($user, $viewRecap);
        $auth->addChild($user, $viewGroup);

        $operator = $auth->createRole('operator'); // this is the back-end user
        $auth->add($operator);
        $auth->addChild($operator, $user);
        $auth->addChild($operator, $openEpic);
        $auth->addChild($operator, $controlEpic);
        $auth->addChild($operator, $controlPerson);
        $auth->addChild($operator, $controlCharacter);
        $auth->addChild($operator, $controlStory);
        $auth->addChild($operator, $controlRecap);
        $auth->addChild($operator, $controlGroup);

        $manager = $auth->createRole('manager'); // this is the person who handles the users and general settings
        $auth->add($manager);
        $auth->addChild($manager, $controlUser);
        $auth->addChild($manager, $operator);

        $administrator = $auth->createRole('administrator'); // this is the person who handles the users, general settings, and managers
        $auth->add($administrator);
        $auth->addChild($administrator, $controlManager);
        $auth->addChild($administrator, $manager);
    }
}
