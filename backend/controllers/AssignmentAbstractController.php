<?php

namespace backend\controllers;

use common\components\EpicAssistance;
use common\models\Character;
use common\models\core\HasEpicControl;
use common\models\Epic;
use common\models\Group;
use common\models\Story;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class AssignmentAbstractController extends CmsController
{
    use EpicAssistance;

    /**
     * @throws HttpException
     */
    protected function checkAccess(HasEpicControl $model): void
    {
        if (!$model->canUserControlYou()) {
            $model::throwExceptionAboutControl();
        }
    }

    /**
     * @throws HttpException
     */
    protected function checkEpicConsistency(HasEpicControl $model, Epic $epic): void
    {
        /** @var Epic $epicFromModel */
        $epicFromModel = $model->getEpic()->one();

        if ($epicFromModel->epic_id <> $epic->epic_id) {
            throw new HttpException(400, Yii::t('app', 'ERROR_WRONG_EPIC'));
        }
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

    /**
     * @param array<int> $ids
     *
     * @return array<int,Group>
     *
     * @throws HttpException
     */
    protected function findGroups(array $ids, Epic $epic): array
    {
        $models = Group::findAll($ids);

        if ($models === null) {
            return [];
        }

        $indexedModels = [];
        foreach ($models as $model) {
            $this->checkAccess($model);
            $this->checkEpicConsistency($model, $epic);
            $indexedModels[$model->group_id] = $model;
        }

        return $indexedModels;
    }

    /**
     * @param array<int> $ids
     *
     * @return array<int,Story>
     *
     * @throws HttpException
     */
    protected function findStories(array $ids, Epic $epic): array
    {
        $models = Story::findAll($ids);

        if ($models === null) {
            return [];
        }

        $indexedModels = [];
        foreach ($models as $model) {
            $this->checkAccess($model);
            $this->checkEpicConsistency($model, $epic);
            $indexedModels[$model->story_id] = $model;
        }

        return $indexedModels;
    }

    /**
     * @throws HttpException
     */
    protected function findCharacter(string $key): Character
    {
        $model = Character::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'CHARACTER_NOT_AVAILABLE'));
        }

        $this->checkAccess($model);

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        return $model;
    }

    /**
     * @throws HttpException
     */
    protected function findGroup(string $key): Group
    {
        $model = Group::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'GROUP_NOT_AVAILABLE'));
        }

        $this->checkAccess($model);

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        return $model;
    }

    /**
     * @throws HttpException
     */
    protected function findStory(string $key): Story
    {
        $model = Story::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'STORY_NOT_AVAILABLE'));
        }

        $this->checkAccess($model);

        $this->selectEpic($model->epic->key, $model->epic_id, $model->epic->name);

        return $model;
    }

    protected function respondBasedOnSuccessAndValidity(bool $success, bool $valid): Response
    {
        return new Response(['statusCode' => $success ? 200 : ($valid ? 400 : 500)]);
    }

    /**
     * @throws ServerErrorHttpException
     */
    protected function respondWithError(string $message): Response
    {
        throw new ServerErrorHttpException($message);
    }

    protected function respondWithSuccess(): Response
    {
        return new Response();
    }

    protected function save(ActiveRecord $assignment): Response
    {
        $valid = true;
        try {
            $valid = $assignment->save();
            $success = $valid;
        } catch (Exception) {
            // todo add logging
            $success = false;
        }

        return $this->respondBasedOnSuccessAndValidity($success, $valid);
    }
}
