<?php

namespace api\controllers;

use common\models\Character;
use common\models\Displayable;
use common\models\Person;
use yii\web\Response;
use api\models\security\Authenticator;
use common\models\Epic;
use common\models\Group;
use common\models\Story;
use Yii;
use yii\base\Exception;
use yii\web\HttpException;

class ApiController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $this->enterJsonMode();
        return $this->processOutput("Front page",
            "This is basic front page. Please choose a functionality you wish to access from 'content' area", [
                [
                    "url" => "characters/{auth-method}/{auth-key}/",
                    "description" => "Lists all characters available in the system"
                ],
                [
                    "url" => "character/{identification-method}/{identification-key}/{auth-method}/{auth-key}/",
                    "description" => "Specific character data"
                ],
                [
                    "url" => "person/{identification-method}/{identification-key}/{auth-method}/{auth-key}/",
                    "description" => "Specific person data"
                ],
            ]);
    }

    public function actionError()
    {
        $this->enterJsonMode();
        return $this->processOutput("Error", "?", "?");
    }

    /**
     * Sets up correct response mode
     */
    protected function enterJsonMode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    /**
     * Output processor.
     * @param string $title
     * @param string $description
     * @param array $content
     * @return array
     */
    protected function processOutput($title, $description, array $content)
    {
        return [
            "title" => $title,
            "description" => $description,
            "content" => $content
        ];
    }

    protected function viewAction($method, $key, $authMethod, $authKey, $objectName, $title, $description) {
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

        $methodName = 'find'.$objectName.'ByKey';

        if(!method_exists($this, $methodName)) {
            throw new HttpException(501, "Method for object '$objectName' not found");
        }

        try {
            /* @var $object Displayable */
            $object = $this->$methodName($key);
        } catch (Exception $e) {
            $errors[] = $e->getName();
            $object = null;
        }

        if($object) {
            $content = $object->getCompleteData();
        } else {
            $content = [
                'errors' => $errors,
            ];
        }

        $this->enterJsonMode();
        return $this->processOutput($title, $description, $content);
    }

    public function actionCharacter($method, $key, $authMethod, $authKey)
    {
        return $this->viewAction($method, $key, $authMethod, $authKey, "Character", "Character data", "Complete data");
    }

    public function actionEpic($method, $key, $authMethod, $authKey)
    {
        return $this->viewAction($method, $key, $authMethod, $authKey, "Epic", "Epic data", "Epic data for epic page and story list");
    }

    public function actionGroup($method, $key, $authMethod, $authKey)
    {
        return $this->viewAction($method, $key, $authMethod, $authKey, "Group", "Group data", "Group data for group page");
    }

    public function actionPerson($method, $key, $authMethod, $authKey)
    {
        return $this->viewAction($method, $key, $authMethod, $authKey, "Person", "Person data", "Complete person data");
    }

    public function actionStory($method, $key, $authMethod, $authKey)
    {
        return $this->viewAction($method, $key, $authMethod, $authKey, "Story", "Story data", "Complete story data");
    }

    /**
     * Finds the Character model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $key
     * @return Character the loaded model
     * @throws Exception
     */
    protected function findCharacterByKey($key)
    {
        if (($model = Character::findOne(['key' => $key])) !== null) {
            return $model;
        } else {
            throw new Exception('The requested epic does not exist.');
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

    /**
     * Finds the Group model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $key
     * @return Group the loaded model
     * @throws Exception
     */
    protected function findGroupByKey($key)
    {
        if (($model = Group::findOne(['key' => $key])) !== null) {
            return $model;
        } else {
            throw new Exception('The requested group does not exist.');
        }
    }

    /**
     * Finds the Person model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $key
     * @return Person the loaded model
     * @throws Exception
     */
    protected function findPersonByKey($key)
    {
        if (($model = Person::findOne(['key' => $key])) !== null) {
            return $model;
        } else {
            throw new Exception('The requested story does not exist.');
        }
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