<?php

namespace common\models\core;

use common\models\Epic;
use yii\db\ActiveQuery;
use yii\web\HttpException;

/**
 * Interface HasEpicControl indicates the rights of the object are handled mainly by its Epic
 * @package common\models\core
 */
interface HasEpicControl
{
    /**
     * Determines whether the user can create a new object of the class
     *
     * @throws HttpException
     */
    static public function canUserCreateThem(): bool;

    /**
     * Determines whether the user can list the objects
     *
     * @throws HttpException
     */
    static public function canUserIndexThem(): bool;

    /**
     * Throws a 403 exception if the user cannot create
     *
     * @throws HttpException
     */
    static function throwExceptionAboutCreate();

    /**
     * Throws an exception if the user cannot create
     *
     * @throws HttpException
     */
    static function throwExceptionAboutControl();

    /**
     * Throws a 403 exception if the user cannot index
     *
     * @throws HttpException
     */
    static function throwExceptionAboutIndex();

    /**
     * Throws a 403 exception if the user cannot view
     *
     * @throws HttpException
     */
    static function throwExceptionAboutView();

    /**
     * Determines whether the user can alter or delete the object
     *
     * @throws HttpException
     */
    public function canUserControlYou(): bool;

    /**
     * Determines whether the user can view the object
     *
     * @throws HttpException
     */
    public function canUserViewYou(): bool;

    /**
     * Provides Epic
     */
    public function getEpic(): ActiveQuery;

    public function setCurrentEpicOnEmpty(): void;

    public function setEpicOnEmpty(Epic $epic): void;

    public function isEpicSet(): bool;
}
