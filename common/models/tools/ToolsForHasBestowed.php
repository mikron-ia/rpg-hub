<?php

namespace common\models\tools;

use common\models\core\Visibility;

trait ToolsForHasBestowed
{
    public function getVisibilityNameWithBestowed(): string
    {
        $visibility = $this->getVisibility();
        $value = $visibility->getName();

        if ($visibility === Visibility::Designated) {
            $value = $this->bestowedList->getBestowedUserNames()
                    |> (fn($names) => !empty($names) ? sprintf('{%s}', implode(', ', $names)) : '∅')
                    |> (fn($names) => sprintf('%s: %s', $value, $names));
        }

        return $value;
    }
}
