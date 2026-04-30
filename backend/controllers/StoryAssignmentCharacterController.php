<?php

namespace backend\controllers;

use common\models\Character;
use common\models\core\Visibility;
use common\models\Epic;
use common\models\StoryCharacterAssignment;
use Override;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;

class StoryAssignmentCharacterController extends StoryAssignmentAbstractController
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
        $visibility = Yii::$app->request->post('visibility');

        $validatedVisibility = Visibility::tryFrom($visibility);

        if ($validatedVisibility === null) {
            throw new BadRequestHttpException(Yii::t('app', 'ERROR_VISIBILITY_NOT_VALID'));
        }

        $story = $this->findStory($storyKey);
        $characters = $this->findCharacters($characterIds, $story->epic);

        $existingAssignments = StoryCharacterAssignment::findAll([
            'story_id' => $story->story_id,
            'visibility' => $validatedVisibility->value,
        ]);

        $characterIdsToUnassign = array_diff(array_column($existingAssignments, 'character_id'), $characterIds);
        $characterIdsToSkip = array_intersect($characterIds, array_column($existingAssignments, 'character_id'));

        try {
            StoryCharacterAssignment::deleteAll([
                'character_id' => $characterIdsToUnassign,
                'visibility' => $validatedVisibility->value,
            ]);

            $unassignedCharacters = $this->findCharacters($characterIdsToUnassign, $story->epic);
            array_walk($unassignedCharacters, function (Character $character) {
                $character->importancePack->flagForRecalculation();
            });

            foreach ($characters as $characterId => $character) {
                if (!in_array($characterId, $characterIdsToSkip)) {
                    StoryCharacterAssignment::create($characterId, $story->story_id, $validatedVisibility);
                    $character->importancePack->flagForRecalculation();
                }
            }
        } catch (Throwable $e) {
            return $this->respondWithError($e->getMessage());
        }

        return $this->respondWithSuccess();
    }

    /**
     * @param array<int> $ids
     *
     * @return array<int,Character>
     *
     * @throws HttpException
     */
    protected function findCharacters(array $ids, Epic $epic): array
    {
        $models = Character::findAll($ids);

        if ($models === null) {
            return [];
        }

        $indexedModels = [];
        foreach ($models as $model) {
            $this->checkAccess($model);
            $this->checkEpicConsistency($model, $epic);
            $indexedModels[$model->character_id] = $model;
        }

        return $indexedModels;
    }
}
