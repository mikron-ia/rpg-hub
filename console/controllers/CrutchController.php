<?php

namespace console\controllers;

use common\models\Character;
use common\models\CharacterSheet;
use common\models\Epic;
use common\models\Group;
use common\models\Recap;
use common\models\Story;
use yii\console\Controller;

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
            $object->save();
        }
    }
    /**
     * Saves all characters
     * @return void
     */
    public function actionSaveCharacterSheets()
    {
        $objects = CharacterSheet::find()->all();

        foreach ($objects as $object) {
            $object->save();
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
            $object->save();
        }
    }

    /**
     * Saves all groups
     * @return void
     */
    public function actionSaveEpics()
    {
        $objects = Epic::find()->all();

        foreach ($objects as $object) {
            $object->save();
        }
    }

    /**
     * Saves all groups
     * @return void
     */
    public function actionSaveRecaps()
    {
        $objects = Recap::find()->all();

        foreach ($objects as $object) {
            $object->save();
        }
    }

    /**
     * Saves all groups
     * @return void
     */
    public function actionSaveStories()
    {
        $objects = Story::find()->all();

        foreach ($objects as $object) {
            $object->save();
        }
    }

    public function actionSaveAll()
    {
        $this->runAction('save-characters');
        $this->runAction('save-character-sheets');
        $this->runAction('save-epics');
        $this->runAction('save-groups');
        $this->runAction('save-recaps');
        $this->runAction('save-stories');
    }
}
