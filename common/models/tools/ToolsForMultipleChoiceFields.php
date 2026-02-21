<?php

namespace common\models\tools;

trait ToolsForMultipleChoiceFields
{
    /**
     * @param array $input
     * @return array<int>
     */
    private function normalizeIntegerInput(array $input): array
    {
        $normalizedIds = [];
        foreach ($input as $value) {
            if (!is_numeric($value)) {
                continue;
            }

            $normalizedIds[] = (int)$value;
        }

        return $normalizedIds;
    }
}
