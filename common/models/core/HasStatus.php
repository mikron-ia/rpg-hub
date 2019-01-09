<?php

namespace common\models\core;


interface HasStatus
{
    /**
     * Provides lists of status names indexed by status
     * @return string[]
     */
    static public function statusNames(): array;

    /**
     * Provides lists of status CSS classes indexed by status
     * @return string[]
     */
    static public function statusClasses(): array;

    /**
     * Provides current status code
     * @return string
     */
    public function getStatus(): string;

    /**
     * Provides class for current status
     * @return string
     */
    public function getStatusClass(): string;

    /**
     * Provides all permitted changes in statuses
     * @return string[][]
     */
    public function statusAllowedChanges(): array;

    /**
     * Provides statuses permitted from the current one
     * @return string[]
     */
    public function getAllowedChange(): array;

    /**
     * Provides names of statuses permitted from the current one
     * @return string[]
     */
    public function getAllowedChangeNames(): array;
}
