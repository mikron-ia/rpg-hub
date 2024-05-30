<?php

namespace console\controllers;

use common\models\Article;
use common\models\Character;
use common\models\CharacterSheet;
use common\models\Epic;
use common\models\Game;
use common\models\Group;
use common\models\Recap;
use common\models\Story;
use yii\base\InvalidRouteException;
use yii\console\Controller;
use yii\console\Exception;

/**
 * Class CrutchController
 *
 * Class used for migration operations that can be done only from console
 *
 * @package console\controllers
 */
class CrutchController extends Controller
{
    /**
     * Saves all characters
     *
     * @return void
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
     * @return void
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
     * @return void
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
     * @return void
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
     * @return void
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
     * @return void
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
     * @return void
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
     * @return void
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
     * Saves everything - to be used to trigger beforeSave() or afterSave() on all
     *
     * @return void
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
    }
}
