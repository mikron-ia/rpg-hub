<?php

namespace common\models\core;

use yii\db\Exception;

/**
 * Interface HasSightings is used for classes with `seen by` system in order to record user sightings of objects
 * @package common\models\core
 */
interface HasSightings
{
    /**
     * Records user viewing the object
     *
     * @return bool
     *
     * @throws Exception
     */
    public function recordSighting(): bool;

    /**
     * Records user listing of viewing the object
     *
     * @return bool
     *
     * @throws Exception
     */
    public function recordNotification(): bool;

    /**
     * Provides a status name in the appropriate language
     *
     * @return string
     */
    public function showSightingStatus(): string;

    /**
     * Provides the CSS class applied to the status tag
     *
     * @return string
     */
    public function showSightingCSS(): string;
}
