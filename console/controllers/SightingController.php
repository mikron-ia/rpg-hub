<?php

namespace console\controllers;

use common\components\LoggingHelper;
use common\models\Article;
use common\models\Character;
use common\models\CharacterSheet;
use common\models\core\HasSightings;
use common\models\Epic;
use common\models\Group;
use common\models\Recap;
use common\models\Story;
use yii\console\Controller;

/**
 * Class ImportanceController
 * @package console\controllers
 */
class SightingController extends Controller
{
    public function actionSupplement()
    {
        LoggingHelper::log("Initiated", 'sighting.completion');

        $articles = Article::find()->all();
        $characters = Character::find()->all();
        $characterSheets = CharacterSheet::find()->all();
        $epics = Epic::find()->all();
        $groups = Group::find()->all();
        $recaps = Recap::find()->all();
        $stories = Story::find()->all();

        $this->createAbsentSightingObjects($articles);
        $this->createAbsentSightingObjects($characters);
        $this->createAbsentSightingObjects($characterSheets);
        $this->createAbsentSightingObjects($epics);
        $this->createAbsentSightingObjects($groups);
        $this->createAbsentSightingObjects($recaps);
        $this->createAbsentSightingObjects($stories);

        LoggingHelper::log("Completed", 'sighting.completion');
    }

    /**
     * @param HasSightings[] $objects
     */
    private function createAbsentSightingObjects(array $objects)
    {
        foreach ($objects as $object) {
            $object->seenPack->createAbsentSightingObjects();
        }
    }
}
