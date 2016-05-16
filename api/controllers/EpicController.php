<?php

namespace api\controllers;

use common\models\Story;
use yii\base\Exception;

class EpicController extends ApiController
{
    public function actionEpic()
    {
        $this->enterJsonMode();
        return $this->processOutput("Epic data", "Epic data for epic page and story list", ["NYI"]);
    }

    public function actionStory($storyKey)
    {
        $errors = [];

        try {
            $story = $this->findStoryByKey($storyKey);
        } catch (Exception $e) {
            $errors[] = $e->getName();
            $story = null;
        }

        if($story) {
            $content = $story->getCompleteData();
        } else {
            $content = [
                'errors' => $errors,
            ];
        }

        $this->enterJsonMode();
        return $this->processOutput("Story data", "Complete story data", $content);
    }

    /**
     * Finds the Story model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $key
     * @return Story the loaded model
     * @throws Exception
     */
    protected function findStoryByKey($key)
    {
        if (($model = Story::findOne(['key' => $key])) !== null) {
            return $model;
        } else {
            throw new Exception('The requested story does not exist.');
        }
    }
}
