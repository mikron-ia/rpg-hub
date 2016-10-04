<?php

namespace common\models\tools;

use Yii;
use yii\web\HttpException;

trait ToolsForEntity
{
    private function generateKey($identifier)
    {
        if (!isset(Yii::$app->params['keyGeneration'][$identifier])) {
            throw new HttpException(500, "Missing configuration for key");
        }

        $pattern = Yii::$app->params['keyGeneration'][$identifier];

        $placeholders = ['number0', 'number1', 'number2', 'number3', 'number4'];
        $values = [mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand()];
        $string = str_replace($placeholders, $values, $pattern);

        return sha1($string);
    }
}
