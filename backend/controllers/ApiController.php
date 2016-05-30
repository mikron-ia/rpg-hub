<?php

namespace backend\controllers;

use common\models\Displayable;
use common\models\Epic;
use common\models\Person;
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

    public function actionCharacter($epicCode, $method, $key, $authMethod, $authKey, $language)
    {
        return $this->viewAction($epicCode, $method, $key, $authMethod, $authKey, "Character", "Character data",
            "Complete data", $language);
    }

    public function actionEpic($epicCode, $method, $key, $authMethod, $authKey, $language)
    {
        return $this->viewAction($epicCode, $method, $key, $authMethod, $authKey, "Epic", "Epic data",
            "Epic data for epic page and story list", $language);
    }

    public function actionGroup($epicCode, $method, $key, $authMethod, $authKey, $language)
    {
        return $this->viewAction($epicCode, $method, $key, $authMethod, $authKey, "Group", "Group data",
            "Group data for group page", $language);
    }

    public function actionPeople($epicCode, $authMethod, $authKey, $language)
    {
        return $this->indexAction($epicCode, $authMethod, $authKey, "Person", "People list", "Complete people list",
            $language);
    }

    public function actionPerson($epicCode, $method, $key, $authMethod, $authKey, $language)
    {
        return $this->viewAction($epicCode, $method, $key, $authMethod, $authKey, "Person", "Person data",
            "Complete person data", $language);
    }

    public function actionStory($epicCode, $method, $key, $authMethod, $authKey, $language)
    {
        return $this->viewAction($epicCode, $method, $key, $authMethod, $authKey, "Story", "Story data",
            "Complete story data", $language);
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

    protected function viewAction(
        $epicCode,
        $method,
        $key,
        $authMethod,
        $authKey,
        $objectName,
        $title,
        $description,
        $language
    ) {
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
            $epicId = $this->getEpicId($epicCode);

            /* @var $object Displayable */
            $object = $this->findSomethingByKey($objectName, $key, $epicId);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
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

    protected function indexAction($epicCode, $authMethod, $authKey, $objectName, $title, $description, $language)
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

        $peopleList = [];

        try {
            $epicId = $this->getEpicId($epicCode);

            /* @var $object Displayable */
            $objects = Person::findAll(['epic_id' => $epicId]);

            foreach ($objects as $object) {
                $peopleList[] = $object->getSimpleData();
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            $objects = null;
        }

        $this->enterJsonMode();
        return $this->processOutput($title, $description, $peopleList);
    }

    /**
     * @param string $epicCode
     * @return int
     * @throws Exception
     * @throws HttpException
     */
    protected function getEpicId($epicCode)
    {
        /* @var $epic Epic */
        $epic = $this->findSomethingByKey('Epic', $epicCode, null);
        return $epic->epic_id;
    }

    /**
     * @param string $something Class name
     * @param string $key DB key
     * @param int $epicId DB ID for the epic
     * @return Displayable
     * @throws Exception
     * @throws HttpException
     */
    protected function findSomethingByKey($something, $key, $epicId)
    {
        $className = 'common\models\\' . $something;

        if (!class_exists($className)) {
            throw new HttpException(501, "Method for object '$className' not found");
        }

        $parameters = ['key' => $key];

        if($something != 'Epic') {
            $parameters['epic_id'] = $epicId;
        }

        if (($model = $className::findOne($parameters)) !== null) {
            return $model;
        } else {
            throw new Exception('The requested ' . $something . ' object does not exist.');
        }
    }
}