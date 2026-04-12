<?php

namespace common\models\tools;

use common\models\Epic;
use Yii;
use yii\web\HttpException;

trait ToolsForEntity
{
    public static function canUserCreateInEpic(Epic $epic): bool
    {
        /* Use of control* right is intentional; there is no need to separate creation from control at this level */
        return Yii::$app->user->can('control' . self::cleanClassName(), ['epic' => $epic]);
    }

    /**
     * Provides a class name that uses the trait
     * The name comes without the namespace
     */
    private static function cleanClassName(): string
    {
        $position = strrpos(static::class, '\\');
        if ($position !== false) {
            return substr(static::class, $position + 1);
        }

        return '';
    }

    public static function canUserControlInEpic(Epic $epic): bool
    {
        return Yii::$app->user->can('control' . self::cleanClassName(), ['epic' => $epic]);
    }

    public static function canUserViewInEpic(Epic $epic): bool
    {
        return Yii::$app->user->can('view' . self::cleanClassName(), ['epic' => $epic]);
    }

    public static function canUserIndexInEpic(Epic $epic): bool
    {
        return Yii::$app->user->can('view' . self::cleanClassName(), ['epic' => $epic]);
    }

    /**
     * @throws HttpException
     */
    private static function thrownExceptionAbout(string $message)
    {
        throw new HttpException(403, $message);
    }

    public function setCurrentEpicOnEmpty(): void
    {
        if (isset(Yii::$app->params['activeEpic']) && $this->epic_id === null) {
            $this->epic_id = Yii::$app->params['activeEpic']->epic_id;
        }
    }

    public function setEpicOnEmpty(Epic $epic): void
    {
        $this->epic_id = $epic->epic_id;
    }

    public function isEpicSet(): bool
    {
        return !empty($this->epic_id);
    }

    /**
     * @throws HttpException
     */
    public function fillInKey(): self
    {
        if (empty($this->key)) {
            $this->key = $this->generateKey();
        }

        return $this;
    }

    /**
     * @throws HttpException
     */
    private function generateKey(?string $identifier = null): string
    {
        if (empty($identifier) && method_exists($this, 'keyParameterName')) {
            $identifier = self::keyParameterName();
        }

        if (empty($identifier)) {
            throw new HttpException(500, "Missing identifier for key generation");
        }

        if (!isset(Yii::$app->params['keyGeneration'][$identifier])) {
            throw new HttpException(500, "Missing configuration for key $identifier");
        }

        $pattern = Yii::$app->params['keyGeneration'][$identifier];

        $placeholders = ['number0', 'number1', 'number2', 'number3', 'number4'];
        $values = [mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand()];
        $string = str_replace($placeholders, $values, $pattern);

        return sha1($string);
    }

    public function getObjectKey(): string
    {
        return $this->key;
    }
}
