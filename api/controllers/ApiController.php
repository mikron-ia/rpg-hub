<?php

namespace api\controllers;

use yii\web\Response;
use Yii;

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
}