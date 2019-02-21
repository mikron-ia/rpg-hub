<?php

namespace common\models\core;

use yii\web\HttpException;

/**
 * Interface HasEpicControl indicates the rights of the object are handled mainly by its Epic
 * @package common\models\core
 */
interface HasEpicControl
{
    /**
     * Determines whether user can create a new object of the class
     * @return bool
     * @throws HttpException
     */
    static public function canUserCreateThem(): bool;

    /**
     * Determines whether user can list the objects
     * @return bool
     * @throws HttpException
     */
    static public function canUserIndexThem(): bool;

    /**
     * Throws a 403 exception if user cannot create
     * @throws HttpException
     */
    static function throwExceptionAboutCreate();

    /**
     * Throws an exception if user cannot create
     * @throws HttpException
     */
    static function throwExceptionAboutControl();

    /**
     * Throws a 403 exception if user cannot index
     * @throws HttpException
     */
    static function throwExceptionAboutIndex();

    /**
     * Throws a 403 exception if user cannot view
     * @throws HttpException
     */
    static function throwExceptionAboutView();

    /**
     * Determines whether user can alter or delete the object
     * @return bool
     * @throws HttpException
     */
    public function canUserControlYou(): bool;

    /**
     * Determines whether user can view the object
     * @return bool
     * @throws HttpException
     */
    public function canUserViewYou(): bool;

    /**
     * Provides Epic
     */
    public function getEpic();
}
