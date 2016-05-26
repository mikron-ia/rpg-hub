<?php

namespace api\controllers;

use api\models\security\Authenticator;
use common\models\Epic;
use common\models\Story;
use Yii;
use yii\base\Exception;
use yii\web\HttpException;

class EpicController extends ApiController
{
    public function actionEpic($method, $key, $authMethod, $authKey)
    {
        if($method !== 'key') {
            throw new HttpException(405, "Only key-based access method is accepted.");
        }

        Authenticator::checkAuthentication(
            Yii::$app->params['authenticationReferences'],
            Yii::$app->params['authentication'],
            $authMethod,
            $authKey
        );

        $errors = [];

        try {
            $story = $this->findEpicByKey($key);
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
        return $this->processOutput("Epic data", "Epic data for epic page and story list", $content);
    }

    public function actionStory($method, $key, $authMethod, $authKey)
    {
        if($method !== 'key') {
            throw new HttpException(405, "Only key-based access method is accepted.");
        }

        Authenticator::checkAuthentication(
            Yii::$app->params['authenticationReferences'],
            Yii::$app->params['authentication'],
            $authMethod,
            $authKey
        );

        $errors = [];

        try {
            $story = $this->findStoryByKey($key);
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

    /**
     * Finds the Epic model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $key
     * @return Epic the loaded model
     * @throws Exception
     */
    protected function findEpicByKey($key)
    {
        if (($model = Epic::findOne(['key' => $key])) !== null) {
            return $model;
        } else {
            throw new Exception('The requested epic does not exist.');
        }
    }
}
