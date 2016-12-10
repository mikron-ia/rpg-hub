<?php

namespace common\models\core;

/**
 * Interface HasSightings is used for classes with `seen by` system in order to record user sightings of objects
 * @package common\models\core
 */
interface HasSightings
{
    /**
     * Records user viewing the object
     * @return bool
     */
    public function recordSighting():bool;

    /**
     * Records user listing of viewing the object
     * @return bool
     */
    public function recordNotification():bool;
}
