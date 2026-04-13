<?php

namespace backend\controllers;

use common\components\EpicAssistance;
use common\models\core\HasEpicControl;
use common\models\Epic;
use common\models\Story;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class StoryAssignmentAbstractController extends CmsController
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
