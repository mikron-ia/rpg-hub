<?php

namespace console\controllers;

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
 * Class UpkeepController
 * @package console\controllers
 */
class UpkeepController extends Controller
{
    /**
     * Things that have to run often, at least once an hour
     */
    public function actionHourly()
    {
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

        exit(0);
    }

    /**
     * Things that have to be run every few hours
     */
    public function actionSemiHourly()
    {
        exit(0);
    }

    /**
     * Things that have to be run daily
     */
    public function actionDaily()
    {
        exit(0);
    }

    /**
     * Things that have to be run every few days
     */
    public function actionSemiDaily()
    {
        exit(0);
    }

    /**
     * Things that have to be run weekly
     */
    public function actionWeekly()
    {
        exit(0);
    }

    /**
     * Things that have to be run monthly
     */
    public function actionMonthly()
    {
        exit(0);
    }

    /**
     * Things that have to be run every few months
     */
    public function actionSemiMonthly()
    {
        exit(0);
    }

    /**
     * Things that have to be run yearly
     */
    public function actionYearly()
    {
        exit(0);
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
