<?php

namespace backend\controllers;

use Yii;
use yii\base\InvalidRouteException;
use yii\web\Controller;
use yii\web\Response;

abstract class CmsController extends Controller
{
    /**
     * @param string[] $default
     */
    protected function returnToReferrer(array $default): Response
    {
        $referrer = Yii::$app->getRequest()->getReferrer();
        if ($referrer) {
            try {
                return Yii::$app->getResponse()->redirect($referrer);
            } catch (InvalidRouteException) {
                return $this->redirect($default);
            }
        }

        return $this->redirect($default);
    }
}
