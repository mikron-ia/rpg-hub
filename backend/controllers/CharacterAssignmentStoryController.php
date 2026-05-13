<?php

namespace backend\controllers;

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

class CharacterAssignmentStoryController extends AssignmentAbstractController
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
                            'get-character-stories',
                            'set-character-stories',
                        ],
                        'allow' => true,
                        'roles' => ['operator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'set-character-stories' => ['PUT'],
                ],
            ],
        ];
    }

    /**
     * @throws HttpException
     */
    public function actionGetCharacterStories(string $characterKey): string
    {
        $model = $this->findCharacter($characterKey);
        $this->checkAccess($model);

        $query = StoryCharacterAssignment::find()
            ->where(['story_character_assignment.character_id' => $model->character_id])
            ->joinWith('story')
            ->orderBy('name ASC');

        return $this->renderAjax('_view_story_list', [
            'dataProvider' => new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
            ]),
        ]);
    }

    /**
     * @throws HttpException
     */
    public function actionSetCharacterStories(): Response
    {
        $storyIds = Yii::$app->request->post('keys', []);
        $characterKey = Yii::$app->request->post('characterKey');
        $rank = Yii::$app->request->post('rank') ?? AssignmentRank::Other->value;
        $visibility = Yii::$app->request->post('visibility');

        $validVisibility = Visibility::tryFrom($visibility);

        if ($validVisibility === null) {
            throw new BadRequestHttpException(Yii::t('app', 'ERROR_VISIBILITY_NOT_VALID'));
        }

        $validRank = AssignmentRank::tryFrom($rank);

        if ($validRank === null) {
            throw new BadRequestHttpException(Yii::t('app', 'ERROR_ASSIGNMENT_RANK_NOT_VALID'));
        }

        $character = $this->findCharacter($characterKey);
        $stories = $this->findStories($storyIds, $character->epic);

        $existingAssignments = StoryCharacterAssignment::findAll([
            'character_id' => $character->character_id,
            'rank' => $validRank->value,
            'visibility' => $validVisibility->value,
        ]);

        $storyIdsToUnassign = array_diff(array_column($existingAssignments, 'story_id'), $storyIds);
        $storyIdsToSkip = array_intersect($storyIds, array_column($existingAssignments, 'story_id'));

        try {
            StoryCharacterAssignment::deleteAll([
                'character_id' => $character->character_id,
                'story_id' => $storyIdsToUnassign,
                'rank' => $validRank->value,
                'visibility' => $validVisibility->value,
            ]);

            foreach ($stories as $storyId => $story) {
                if (!in_array($storyId, $storyIdsToSkip)) {
                    StoryCharacterAssignment::create($character->character_id, $storyId, $validVisibility, $validRank);
                }
            }

            $character->importancePack->flagForRecalculation();
        } catch (Throwable $e) {
            return $this->respondWithError($e->getMessage());
        }

        return $this->respondWithSuccess();
    }
}
