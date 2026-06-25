<?php

namespace common\models\tools;

use common\models\core\Visibility;

trait ToolsForHasVisibility
{
    /**
     * @note Override as needed, this is the default state
     *
     * @return array<Visibility>
     */
    public static function allowedVisibilities(): array
    {
        return [
            Visibility::GameMaster,
            Visibility::Full,
        ];
    }

    /**
     * @return array<string>
     */
    public static function allowedVisibilitiesForValidator(): array
    {
        return array_map(function (Visibility $visibility) {
            return $visibility->value;
        }, self::allowedVisibilities());
    }

    public function getVisibility(): Visibility
    {
        return Visibility::from($this->visibility);
    }

    public function getVisibilityName(): string
    {
        return $this->getVisibility()->getName() ?? '?';
    }

    public function getVisibilityLowercase(): string
    {
        return $this->getVisibility()->getNameLowercase() ?? '?';
    }
}
