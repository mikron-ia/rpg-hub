<?php

namespace common\models\core;

use yii\db\ActiveQuery;

/**
 * Interface HasDescriptions describes classes with attached DescriptionPack that want to use full functionality of the pack
 *
 * @package common\models\core
 */
interface HasDescriptions
{
    /**
     * Provides list of types allowed by this class
     *
     * Types should be listed in order desired in select
     *
     * @return string[]
     */
    static public function allowedDescriptionTypes(): array;

    /**
     * Provides ID of the description pack
     */
    public function getDescriptionPackId(): int;

    /**
     * Provides list of visible descriptions from Description Pack
     */
    public function getDescriptionsVisible(): ActiveQuery;
}
