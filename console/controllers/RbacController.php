<?php

namespace console\controllers;

use common\rules\EpicAssistant;
use common\rules\EpicGameMaster;
use common\rules\EpicParticipant;
use common\rules\EpicPlayer;
use common\rules\EpicWatcher;
use Yii;
use yii\console\Controller;

/**
 * Class RbacController
 * Initiates and sets all access rights
 *
 * @package console\controllers
 */
class RbacController extends Controller
{
    /**
     * Clears all rights and rules and sets them again
     */
    public function actionReset()
    {
        $this->actionClear();
        $this->actionInit();
    }

    /**
     * Clears all rights and rules
     */
    public function actionClear()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();
    }

    /**
     * Initiates all rules. NOTE: this will erase all access rights
     * Minor technical note: if there is no separate index* right, all view* rights should allow listing,
     * of course not necessarily complete.
     */
    public function actionInit()
    {
        /* Load v0.4.0 and older */
        $this->actionV040();

        /* Load v0.7.0 */
        $this->actionV080();

        /* Set up the administrator */
        $this->actionSetAdministrator();
    }

    /**
     * Adds rights from v0.4.0 and earlier
     */
    public function actionV040()
    {
        $auth = Yii::$app->authManager;

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

        $controlEpic = $auth->createPermission('controlEpic');
        $controlEpic->description = 'Able to edit and command an epic';
        $controlEpic->ruleName = $gameMasterRule->name;

        $auth->add($controlEpic);

        $indexEpic = $auth->createPermission('indexEpic');
        $indexEpic->description = 'Able to list epics';

        $auth->add($indexEpic);

        $viewEpic = $auth->createPermission('viewEpic');
        $viewEpic->description = 'Able to view an epic';
        $viewEpic->ruleName = $watcherRule->name;

        $auth->add($viewEpic);

        $controlCharacter = $auth->createPermission('controlCharacter');
        $controlCharacter->description = 'Able to add, edit, and remove a character';
        $controlCharacter->ruleName = $gameMasterRule->name;

        $auth->add($controlCharacter);

        $viewCharacter = $auth->createPermission('viewCharacter');
        $viewCharacter->description = 'Able to view a character';
        $viewCharacter->ruleName = $watcherRule->name;

        $auth->add($viewCharacter);

        $controlCharacterSheet = $auth->createPermission('controlCharacterSheet');
        $controlCharacterSheet->description = 'Able to add, edit, and remove a character sheet';
        $controlCharacterSheet->ruleName = $gameMasterRule->name;

        $auth->add($controlCharacterSheet);

        $viewCharacterSheet = $auth->createPermission('viewCharacterSheet');
        $viewCharacterSheet->description = 'View a character sheet';
        $viewCharacterSheet->ruleName = $playerRule->name;

        $auth->add($viewCharacterSheet);

        $controlStory = $auth->createPermission('controlStory');
        $controlStory->description = 'Able to add, edit, and move a story';
        $controlStory->ruleName = $gameMasterRule->name;

        $auth->add($controlStory);

        $viewStory = $auth->createPermission('viewStory');
        $viewStory->description = 'Able to view a story';
        $viewStory->ruleName = $watcherRule->name;

        $auth->add($viewStory);

        $controlRecap = $auth->createPermission('controlRecap');
        $controlRecap->description = 'Able to add, edit, and move a recap';
        $controlRecap->ruleName = $gameMasterRule->name;

        $auth->add($controlRecap);

        $viewRecap = $auth->createPermission('viewRecap');
        $viewRecap->description = 'Able to view a recap';
        $viewRecap->ruleName = $watcherRule->name;

        $auth->add($viewRecap);

        $controlGroup = $auth->createPermission('controlGroup');
        $controlGroup->description = 'Able to add, edit, and remove a group; includes rights to add and remove members';
        $controlGroup->ruleName = $gameMasterRule->name;

        $auth->add($controlGroup);

        $viewGroup = $auth->createPermission('viewGroup');
        $viewGroup->description = 'Able to view group';
        $viewGroup->ruleName = $watcherRule->name;

        $auth->add($viewGroup);

        $controlSession = $auth->createPermission('controlSession');
        $controlSession->description = 'Able to announce session and control its execution';
        $controlSession->ruleName = $assistantRule->name;

        $auth->add($controlSession);

        $viewSession = $auth->createPermission('viewSession');
        $viewSession->description = 'Able view sessions';
        $viewSession->ruleName = $watcherRule->name;

        $auth->add($viewSession);

        /* General actions */

        $openEpic = $auth->createPermission('openEpic');
        $openEpic->description = 'Able to add and open an epic';

        $auth->add($openEpic);

        $controlUser = $auth->createPermission('controlUser');
        $controlUser->description = 'Able to add, edit, and manage a user';

        $auth->add($controlUser);

        $controlManager = $auth->createPermission('controlManager');
        $controlManager->description = 'Able to add, edit, and manage a manager';

        $auth->add($controlManager);

        $listPerformedActions = $auth->createPermission('listPerformedActions');
        $listPerformedActions->description = 'Able to list performed actions';

        $auth->add($listPerformedActions);

        /* Roles */

        $user = $auth->createRole('user');
        $user->description = 'The basic user, confined to the front';

        $auth->add($user);

        $auth->addChild($user, $viewEpic);
        $auth->addChild($user, $viewCharacter);
        $auth->addChild($user, $viewCharacterSheet);
        $auth->addChild($user, $viewStory);
        $auth->addChild($user, $viewRecap);
        $auth->addChild($user, $viewGroup);
        $auth->addChild($user, $viewSession);

        $operator = $auth->createRole('operator');
        $user->description = 'The back-end user';

        $auth->add($operator);

        $auth->addChild($operator, $user);

        $auth->addChild($operator, $openEpic);
        $auth->addChild($operator, $controlEpic);
        $auth->addChild($operator, $controlCharacter);
        $auth->addChild($operator, $controlCharacterSheet);
        $auth->addChild($operator, $controlStory);
        $auth->addChild($operator, $controlRecap);
        $auth->addChild($operator, $controlGroup);
        $auth->addChild($operator, $controlSession);
        $auth->addChild($operator, $indexEpic);

        $manager = $auth->createRole('manager');
        $user->description = 'The person who handles the users and general settings';

        $auth->add($manager);

        $auth->addChild($manager, $operator);

        $auth->addChild($manager, $controlUser);
        $auth->addChild($manager, $listPerformedActions);

        $administrator = $auth->createRole('administrator');
        $user->description = 'The person who handles the users, general settings, and managers';

        $auth->add($administrator);

        $auth->addChild($administrator, $manager);

        $auth->addChild($administrator, $controlManager);
    }

    /**
     * Adds rights from v0.8.0
     */
    public function actionV080()
    {
        $auth = Yii::$app->authManager;

        $gameMasterRule = $auth->getRule('epicGameMaster');
        $watcherRule = $auth->getRule('epicWatcher');

        $controlScenario = $auth->createPermission('controlScenario');
        $controlScenario->description = 'Able to add, edit, and move a scenario';
        $controlScenario->ruleName = $gameMasterRule->name;

        $auth->add($controlScenario);

        $viewScenario = $auth->createPermission('viewScenario');
        $viewScenario->description = 'Able to view a scenario';
        $viewScenario->ruleName = $watcherRule->name;

        $auth->add($viewScenario);

        $controlGame = $auth->createPermission('controlGame');
        $controlGame->description = 'Able to add, edit, and move a game';
        $controlGame->ruleName = $gameMasterRule->name;

        $auth->add($controlGame);

        $viewGame = $auth->createPermission('viewGame');
        $viewGame->description = 'Able to view a game';
        $viewGame->ruleName = $watcherRule->name;

        $auth->add($viewGame);

        $user = $auth->getRole('user');
        $operator = $auth->getRole('operator');

        $auth->addChild($operator, $controlScenario);
        $auth->addChild($operator, $viewScenario);

        $auth->addChild($operator, $controlGame);
        $auth->addChild($user, $viewGame);
    }

    /**
     * Sets administrator role to used with ID == 1. Intended for development and production initialisation.
     */
    public function actionSetAdministrator()
    {
        $auth = Yii::$app->authManager;
        $administrator = $auth->getRole('administrator');
        $auth->assign($administrator, 1);
    }

    /**
     * Adds rights from v0.5.0
     */
    public function actionV050()
    {
    }

    /**
     * Adds rights from v0.6.0
     */
    public function actionV060()
    {
    }

    /**
     * Adds rights from v0.7.0
     */
    public function actionV070()
    {
    }

    /**
     * Adds rights from v0.9.0
     */
    public function actionV090()
    {
    }

    /**
     * Adds rights from v0.10.0
     */
    public function actionV0100()
    {
    }

    /**
     * Adds rights from v0.11.0
     */
    public function actionV0110()
    {
        $auth = Yii::$app->authManager;

        $gameMasterRule = $auth->getRule('epicGameMaster');
        $watcherRule = $auth->getRule('epicWatcher');

        $controlPointInTime = $auth->createPermission('controlPointInTime');
        $controlPointInTime->description = 'Able to add, edit, and move point in time';
        $controlPointInTime->ruleName = $gameMasterRule->name;

        $viewPointInTime = $auth->createPermission('viewPointInTime');
        $viewPointInTime->description = 'Able to view point in time';
        $viewPointInTime->ruleName = $watcherRule->name;

        $user = $auth->getRole('user');
        $operator = $auth->getRole('operator');

        $auth->addChild($operator, $controlPointInTime);
        $auth->addChild($user, $viewPointInTime);
    }
}
