<?php

namespace common\models\tools;

use common\models\Epic;
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

    static public function canUserCreate()
    {
        /* Use of control* right is intentional; there is no need to separate creation from control at this level */
        if (Yii::$app->user->can('control' . self::cleanClassName())) {
            return true;
        } else {
            throw new HttpException(401, Yii::t('app', 'NO_RIGHT_TO_CREATE_OBJECT'));
        }
    }

    /**
     * @param Epic $epic
     * @param string $message
     * @return bool
     * @throws HttpException
     */
    static public function canUserControl($epic, $message)
    {
        if (Yii::$app->user->can('control' . self::cleanClassName(), ['epic' => $epic])) {
            return true;
        } else {
            throw new HttpException(401, $message);
        }
    }

    /**
     * @param Epic $epic
     * @param string $message
     * @return bool
     * @throws HttpException
     */
    static public function canUserView($epic, $message)
    {
        if (Yii::$app->user->can('control' . self::cleanClassName(), ['epic' => $epic])) {
            return true;
        } else {
            throw new HttpException(401, $message);
        }
    }

    /**
     * @param Epic $epic
     * @param string $message
     * @return bool
     * @throws HttpException
     */
    static public function canUserIndex($epic, $message)
    {
        if (Yii::$app->user->can('view' . self::cleanClassName(), ['epic' => $epic])) {
            return true;
        } else {
            throw new HttpException(401, $message);
        }
    }

    /**
     * Provides class name that uses the trait
     * Name comes without namespace
     * @return string
     * @todo Verify if protected or private would not be sufficient, if so, apply most restrictive possible
     */
    static private function cleanClassName()
    {
        $position = strrpos(static::class, '\\');
        if ($position !== null) {
            return substr(static::class, $position + 1);
        } else {
            return '';
        }
    }
}
