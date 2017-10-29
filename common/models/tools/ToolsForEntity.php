<?php

namespace common\models\tools;

use common\models\Epic;
use Yii;
use yii\web\HttpException;

/**
 * Trait ToolsForEntity
 * @package common\models\tools
 */
trait ToolsForEntity
{
    /**
     * @param $identifier
     * @return string
     * @throws HttpException
     */
    private function generateKey($identifier): string
    {
        if (!isset(Yii::$app->params['keyGeneration'][$identifier])) {
            throw new HttpException(500, "Missing configuration for key $identifier");
        }

        $pattern = Yii::$app->params['keyGeneration'][$identifier];

        $placeholders = ['number0', 'number1', 'number2', 'number3', 'number4'];
        $values = [mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand()];
        $string = str_replace($placeholders, $values, $pattern);

        return sha1($string);
    }

    /**
     * @param $epic
     * @return bool
     * @throws HttpException
     */
    static public function canUserCreateInEpic($epic): bool
    {
        /* Use of control* right is intentional; there is no need to separate creation from control at this level */
        if (Yii::$app->user->can('control' . self::cleanClassName(), ['epic' => $epic])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Epic $epic
     * @return bool
     * @throws HttpException
     */
    static public function canUserControlInEpic($epic): bool
    {
        if (Yii::$app->user->can('control' . self::cleanClassName(), ['epic' => $epic])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Epic $epic
     * @return bool
     * @throws HttpException
     */
    static public function canUserViewInEpic($epic): bool
    {
        if (Yii::$app->user->can('view' . self::cleanClassName(), ['epic' => $epic])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Epic $epic
     * @return bool
     * @throws HttpException
     */
    static public function canUserIndexInEpic($epic): bool
    {
        if (Yii::$app->user->can('view' . self::cleanClassName(), ['epic' => $epic])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Provides class name that uses the trait
     * Name comes without namespace
     * @return string
     */
    static private function cleanClassName(): string
    {
        $position = strrpos(static::class, '\\');
        if ($position !== null) {
            return substr(static::class, $position + 1);
        } else {
            return '';
        }
    }

    /**
     * @param $message
     * @throws HttpException
     */
    static private function thrownExceptionAbout($message)
    {
        throw new HttpException(401, $message);
    }

    public function setCurrentEpicOnEmpty()
    {
        if (isset(Yii::$app->params['activeEpic']) && $this->epic_id === null) {
            $this->epic_id = Yii::$app->params['activeEpic']->epic_id;
        }
    }
}
