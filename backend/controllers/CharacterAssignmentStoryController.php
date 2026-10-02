<?php

namespace backend\controllers;

use common\models\StoryCharacterAssignment;
use Override;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\Response;

final class CharacterAssignmentStoryController extends AssignmentAbstractController
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
            'showDuplicateWarning' => $this->hasDuplicateAssignments($query->all()),
        ]);
    }

    /**
     * @throws HttpException
     */
    public function actionSetCharacterStories(): Response
    {
        $storyIds = Yii::$app->request->post('keys', []);
        $characterKey = Yii::$app->request->post('characterKey', '');

        $validRank = $this->processRank(Yii::$app->request);
        $validVisibility = $this->processVisibility(Yii::$app->request);

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
