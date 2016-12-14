<?php

namespace console\controllers;

use common\models\Character;
use common\models\Group;
use yii\console\Controller;

class CrutchController extends Controller
{
    /**
     * Saves all characters
     * This is generally used to trigger all beforeSave and afterSave methods
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
     * Saves all groups
     * This is generally used to trigger all beforeSave and afterSave methods
     * @return void
     */
    public function actionSaveGroups()
    {
        $objects = Group::find()->all();

        foreach ($objects as $object) {
            $object->save();
        }
    }

    public function actionSaveAll()
    {
        $this->runAction('save-characters');
        $this->runAction('save-groups');
    }
}
