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
}