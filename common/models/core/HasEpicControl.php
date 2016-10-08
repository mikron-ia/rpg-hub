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
    static public function canUserCreateThem();

    /**
     * Determines whether user can alter or delete the object
     * @return bool
     * @throws HttpException
     */
    public function canUserControlYou();

    /**
     * Determines whether user can list the objects
     * @return bool
     * @throws HttpException
     */
    static public function canUserIndexThem();

    /**
     * Determines whether user can view the object
     * @return bool
     * @throws HttpException
     */
    public function canUserViewYou();
}
