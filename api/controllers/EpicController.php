<?php

namespace api\controllers;

class EpicController extends ApiController
{
    public function actionEpic()
    {
        $this->enterJsonMode();
        return $this->processOutput("Epic data", "Epic data for epic page and story list", ["NYI"]);
    }

    public function actionStory()
    {
        $this->enterJsonMode();
        return $this->processOutput("Story data", "Complete story data", ["NYI"]);
    }

}
