<?php

namespace common\models\core;

use DateTimeImmutable;

/**
 * Interface HasImportance is used for classes with importance mechanics
 *
 * @package common\models\core
 */
interface HasImportance
{
    /**
     * Provides importance category
     *
     * @return ImportanceCategory
     */
    public function getImportanceCategoryObject(): ImportanceCategory;

    /**
     * Provides the moment of the most recent modification of the object
     * @return DateTimeImmutable
     */
    public function getLastModified(): DateTimeImmutable;

    /**
     * @param int $userId
     * @return string
     */
    public function getSeenStatusForUser(int $userId): string;
}
