<?php

namespace console\controllers;

use common\models\Article;
use common\models\Character;
use common\models\CharacterSheet;
use common\models\Epic;
use common\models\Game;
use common\models\Group;
use common\models\Parameter;
use common\models\Recap;
use common\models\Story;
use yii\base\InvalidRouteException;
use yii\console\Controller;
use yii\console\Exception;
use yii\db\Exception as DbException;

/**
 * Class CrutchController
 *
 * Class used for migration operations that can be done only from the console
 *
 * @package console\controllers
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
     * Saves all games
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
     * Saves everything - to be used to trigger beforeSave() or afterSave() on all
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
    }
}
