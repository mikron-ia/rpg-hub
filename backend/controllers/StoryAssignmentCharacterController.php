<?php

namespace backend\controllers;

use common\models\Character;
use common\models\core\Visibility;
use common\models\StoryCharacterAssignment;
use common\models\type\AssignmentRank;
use Override;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;

class StoryAssignmentCharacterController extends AssignmentAbstractController
{
    #[Override]
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'get-story-characters',
                            'set-story-characters',
                        ],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'set-story-characters' => ['PUT'],
                ],
            ],
        ];
    }

    /**
     * @throws HttpException
     */
    public function actionGetStoryCharacters(string $storyKey): string
    {
        $model = $this->findStory($storyKey);
        $this->checkAccess($model);

        $query = StoryCharacterAssignment::find()
            ->where(['story_id' => $model->story_id])
            ->joinWith('character')
            ->orderBy('name ASC');

        return $this->renderAjax('_view_character_list', [
            'dataProvider' => new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
            ]),
        ]);
    }

    /**
     * @throws HttpException
     */
    public function actionSetStoryCharacters(): Response
    {
        $characterIds = Yii::$app->request->post('keys', []);
        $storyKey = Yii::$app->request->post('storyKey');
        $rank = Yii::$app->request->post('rank');
        $visibility = Yii::$app->request->post('visibility');

        $validVisibility = Visibility::tryFrom($visibility);
        if ($validVisibility === null) {
            throw new BadRequestHttpException(Yii::t('app', 'ERROR_VISIBILITY_NOT_VALID'));
        }

        $validRank = AssignmentRank::tryFrom($rank);
        if ($validRank === null) {
            throw new BadRequestHttpException(Yii::t('app', 'ERROR_ASSIGNMENT_RANK_NOT_VALID'));
        }

        $story = $this->findStory($storyKey);
        $characters = $this->findCharacters($characterIds, $story->epic);

        $existingAssignments = StoryCharacterAssignment::findAll([
            'story_id' => $story->story_id,
            'rank' => $validRank->value,
            'visibility' => $validVisibility->value,
        ]);

        $characterIdsToUnassign = array_diff(array_column($existingAssignments, 'character_id'), $characterIds);
        $characterIdsToSkip = array_intersect($characterIds, array_column($existingAssignments, 'character_id'));

        try {
            StoryCharacterAssignment::deleteAll([
                'character_id' => $characterIdsToUnassign,
                'story_id' => $story->story_id,
                'rank' => $validRank->value,
                'visibility' => $validVisibility->value,
            ]);

            $unassignedCharacters = $this->findCharacters($characterIdsToUnassign, $story->epic);
            array_walk($unassignedCharacters, function (Character $character) {
                $character->importancePack->flagForRecalculation();
            });

            foreach ($characters as $characterId => $character) {
                if (!in_array($characterId, $characterIdsToSkip)) {
                    StoryCharacterAssignment::create($characterId, $story->story_id, $validVisibility, $validRank);
                    $character->importancePack->flagForRecalculation();
                }
            }
        } catch (Throwable $e) {
            return $this->respondWithError($e->getMessage());
        }

        return $this->respondWithSuccess();
    }
}
