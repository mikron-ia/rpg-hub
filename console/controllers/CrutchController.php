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
use yii\console\Controller;

/**
 * Class CrutchController
 * Class used for migration operations that can be done only from console
 * @package console\controllers
 */
class CrutchController extends Controller
{
    /**
     * Saves all characters
     * @return void
     */
    public function actionSaveCharacters()
    {
        $objects = Character::find()->all();

        foreach ($objects as $object) {
            $object->save(false);
        }
    }

    /**
     * Saves all character sheets
     * @return void
     */
    public function actionSaveCharacterSheets()
    {
        $objects = CharacterSheet::find()->all();

        foreach ($objects as $object) {
            $object->save(false);
        }
    }

    /**
     * Saves all groups
     * @return void
     */
    public function actionSaveGroups()
    {
        $objects = Group::find()->all();

        foreach ($objects as $object) {
            $object->save(false);
        }
    }

    /**
     * Saves all epics
     * @return void
     */
    public function actionSaveEpics()
    {
        $objects = Epic::find()->all();

        foreach ($objects as $object) {
            $object->save(false);
        }
    }

    /**
     * Saves all recaps
     * @return void
     */
    public function actionSaveRecaps()
    {
        $objects = Recap::find()->all();

        foreach ($objects as $object) {
            $object->save(false);
        }
    }

    /**
     * Saves all stories
     * @return void
     */
    public function actionSaveStories()
    {
        $objects = Story::find()->all();

        foreach ($objects as $object) {
            $object->save(false);
        }
    }

    /**
     * Saves all articles
     * @return void
     */
    public function actionSaveArticles()
    {
        $objects = Article::find()->all();

        foreach ($objects as $object) {
            $object->save(false);
        }
    }

    /**
     * Saves all games
     * @return void
     */
    public function actionSaveGames()
    {
        $objects = Game::find()->all();

        foreach ($objects as $object) {
            $object->save(false);
        }
    }

    /**
     * Saves everything - to be used to trigger beforeSave() or afterSave() on all
     * @return void
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     */
    public function actionSaveAll()
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
