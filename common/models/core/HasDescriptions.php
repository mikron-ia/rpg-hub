<?php

namespace common\models\core;

use common\models\type\DescriptionType;
use yii\db\ActiveQuery;

/**
 * Interface HasDescriptions describes classes with attached DescriptionPack that want to use full functionality of the pack
 */
interface HasDescriptions
{
    /**
     * Provides the list of types allowed by this class
     * Types will be displayed in form selector in the same order they are provided in this method
     *
     * @return DescriptionType[]
     */
    public static function allowedDescriptionTypes(): array;

    /**
     * Provides the list of visible descriptions from Description Pack
     */
    public function getDescriptionsVisible(): ActiveQuery;

    /**
     * Provides the list of visible and unexpired descriptions from Description Pack
     */
    public function getDescriptionsVisibleForCompact(): ActiveQuery;

    public function getObjectKey(): string;
}
