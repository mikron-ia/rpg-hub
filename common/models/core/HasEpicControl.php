<?php

namespace common\models\core;

use common\models\Epic;
use yii\db\ActiveQuery;
use yii\web\HttpException;

/**
 * Indicates the rights of the object are handled mainly by its Epic
 */
interface HasEpicControl
{
    /**
     * Determines whether the user can create a new object of the class
     *
     * @throws HttpException
     */
    public static function canUserCreateThem(): bool;

    /**
     * Determines whether the user can list the objects
     *
     * @throws HttpException
     */
    public static function canUserIndexThem(): bool;

    /**
     * Throws a 403 exception if the user cannot create
     *
     * @throws HttpException
     */
    public static function throwExceptionAboutCreate();

    /**
     * Throws an exception if the user cannot create
     *
     * @throws HttpException
     */
    public static function throwExceptionAboutControl();

    /**
     * Throws a 403 exception if the user cannot index
     *
     * @throws HttpException
     */
    public static function throwExceptionAboutIndex();

    /**
     * Throws a 403 exception if the user cannot view
     *
     * @throws HttpException
     */
    public static function throwExceptionAboutView();

    /**
     * Determines whether the user can alter or delete the object
     */
    public function canUserControlYou(): bool;

    /**
     * Determines whether the user can view the object
     */
    public function canUserViewYou(): bool;

    public function getEpic(): ActiveQuery;

    public function setCurrentEpicOnEmpty(): void;

    public function setEpicOnEmpty(Epic $epic): void;

    public function isEpicSet(): bool;
}
