<?php

namespace console\controllers;

use common\models\Article;
use common\models\Character;
use common\models\CharacterSheet;
use common\models\Epic;
use common\models\Game;
use common\models\Group;
use common\models\Location;
use common\models\Parameter;
use common\models\Recap;
use common\models\Secret;
use common\models\Story;
use yii\base\InvalidRouteException;
use yii\console\Controller;
use yii\console\Exception;
use yii\db\Exception as DbException;

/**
 * Class used for operations that sometimes have to be run at version change
 *
 * Those actions are intended to emulate object behaviors that would normally happen if they were created with the
 * new code in place. In the current state of things, this means running off-the-record saves to trigger the
 * `beforeSave()` and `afterSave()` methods. Note that saving them still updates some of their timestamps and may change
 * the order in which they appear on the CMS lists. It does not trigger their _updated_ flag, though.
 */
class CrutchController extends Controller
{
    /**
     * Saves all characters
     *
     * @throws DbException
     */
    public function actionSaveCharacters(): void
    {
        $objects = Character::find()->all();

        foreach ($objects as $object) {
            /** @var Character $object */
            $object->is_off_the_record_change = true;
            $object->save(false);
        }
    }

    /**
     * Saves all character sheets
     *
     * @throws DbException
     */
    public function actionSaveCharacterSheets(): void
    {
        $objects = CharacterSheet::find()->all();

        foreach ($objects as $object) {
            /** @var CharacterSheet $object */
            $object->save(false);
        }
    }

    /**
     * Saves all groups
     *
     * @throws DbException
     */
    public function actionSaveGroups(): void
    {
        $objects = Group::find()->all();

        foreach ($objects as $object) {
            /** @var Group $object */
            $object->is_off_the_record_change = true;
            $object->save(false);
        }
    }

    /**
     * Saves all epics
     *
     * @throws DbException
     */
    public function actionSaveEpics(): void
    {
        $objects = Epic::find()->all();

        foreach ($objects as $object) {
            /** @var Epic $object */
            $object->save(false);
        }
    }

    /**
     * Saves all recaps
     *
     * @throws DbException
     */
    public function actionSaveRecaps(): void
    {
        $objects = Recap::find()->all();

        foreach ($objects as $object) {
            /** @var Recap $object */
            $object->save(false);
        }
    }

    /**
     * Saves all stories
     *
     * @throws DbException
     */
    public function actionSaveStories(): void
    {
        $objects = Story::find()->all();

        foreach ($objects as $object) {
            /** @var Story $object */
            $object->is_off_the_record_change = true;
            $object->save(false);
        }
    }

    /**
     * Saves all articles
     *
     * @throws DbException
     */
    public function actionSaveArticles(): void
    {
        $objects = Article::find()->all();

        foreach ($objects as $object) {
            /** @var Article $object */
            $object->is_off_the_record_change = true;
            $object->save(false);
        }
    }

    /**
     * Saves all games (sessions)
     *
     * @throws DbException
     */
    public function actionSaveGames(): void
    {
        $objects = Game::find()->all();

        foreach ($objects as $object) {
            /** @var Game $object */
            $object->save(false);
        }
    }

    /**
     * Saves all parameters
     *
     * @throws DbException
     */
    public function actionSaveParameters(): void
    {
        $objects = Parameter::find()->all();

        foreach ($objects as $object) {
            /** @var Parameter $object */
            $object->save(false);
        }
    }

    /**
     * Saves all locations
     *
     * @throws DbException
     */
    public function actionSaveLocations(): void
    {
        $objects = Location::find()->all();

        foreach ($objects as $object) {
            /** @var Location $object */
            $object->save(false);
        }
    }

    /**
     * Saves all secrets
     *
     * @throws DbException
     */
    public function actionSaveSecrets(): void
    {
        $objects = Secret::find()->all();

        foreach ($objects as $object) {
            /** @var Secret $object */
            $object->save(false);
        }
    }

    /**
     * Triggers all the other save actions from this category
     *
     * @throws InvalidRouteException
     * @throws Exception
     */
    public function actionSaveAll(): void
    {
        $this->runAction('save-epics');
        $this->runAction('save-stories');
        $this->runAction('save-recaps');
        $this->runAction('save-characters');
        $this->runAction('save-character-sheets');
        $this->runAction('save-groups');
        $this->runAction('save-articles');
        $this->runAction('save-games');
        $this->runAction('save-parameters');
        $this->runAction('save-locations');
        $this->runAction('save-secrets');
    }
}
