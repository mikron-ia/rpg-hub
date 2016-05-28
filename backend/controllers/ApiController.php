<?php

namespace backend\controllers;

use common\models\Displayable;
use yii\web\Response;
use backend\models\security\Authenticator;
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

    protected function viewAction($method, $key, $authMethod, $authKey, $objectName, $title, $description, $language)
    {
        Yii::$app->language = $language;

        if ($method !== 'key') {
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
            /* @var $object Displayable */
            $object = $this->findSomethingByKey($objectName, $key);
        } catch (Exception $e) {
            $errors[] = $e->getName();
            $object = null;
        }

        if ($object) {
            $content = $object->getCompleteData();
        } else {
            $content = [
                'errors' => $errors,
            ];
        }

        $this->enterJsonMode();
        return $this->processOutput($title, $description, $content);
    }

    public function actionCharacter($method, $key, $authMethod, $authKey, $language)
    {
        return $this->viewAction($method, $key, $authMethod, $authKey, "Character", "Character data", "Complete data", $language);
    }

    public function actionEpic($method, $key, $authMethod, $authKey, $language)
    {
        return $this->viewAction($method, $key, $authMethod, $authKey, "Epic", "Epic data", "Epic data for epic page and story list", $language);
    }

    public function actionGroup($method, $key, $authMethod, $authKey, $language)
    {
        return $this->viewAction($method, $key, $authMethod, $authKey, "Group", "Group data", "Group data for group page", $language);
    }

    public function actionPeople($authMethod, $authKey, $language)
    {
        return $this->indexAction($authMethod, $authKey, "Person", "People list", "Complete people list", $language);
    }

    public function actionPerson($method, $key, $authMethod, $authKey, $language)
    {
        return $this->viewAction($method, $key, $authMethod, $authKey, "Person", "Person data", "Complete person data", $language);
    }

    public function actionStory($method, $key, $authMethod, $authKey, $language)
    {
        return $this->viewAction($method, $key, $authMethod, $authKey, "Story", "Story data", "Complete story data", $language);
    }

    protected function indexAction($authMethod, $authKey, $objectName, $title, $description, $language)
    {
        Yii::$app->language = $language;

        Authenticator::checkAuthentication(
            Yii::$app->params['authenticationReferences'],
            Yii::$app->params['authentication'],
            $authMethod,
            $authKey
        );

        $errors = [];

        $className = 'common\models\\' . $objectName;

        if (!class_exists($className)) {
            throw new HttpException(501, "Method for object '$objectName' not found");
        }

        try {
            /* @var $object Displayable */
            $objects = $className::find()->all();

            foreach ($objects as $object) {
                $peopleList[] = $object->getSimpleData();
            }

        } catch (Exception $e) {
            $errors[] = $e->getName();
            $objects = null;
        }

        $this->enterJsonMode();
        return $this->processOutput($title, $description, $peopleList);
    }

    protected function findSomethingByKey($something, $key)
    {
        $className = 'common\models\\' . $something;

        if (!class_exists($className)) {
            throw new HttpException(501, "Method for object '$className' not found");
        }

        if (($model = $className::findOne(['key' => $key])) !== null) {
            return $model;
        } else {
            throw new Exception('The requested ' . $className . ' object does not exist.');
        }
    }
}