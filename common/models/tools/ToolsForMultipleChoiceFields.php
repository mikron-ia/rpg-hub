<?php

namespace common\models\tools;

trait ToolsForMultipleChoiceFields
{
    /**
     * Useful for handling a problem with empty lists
     * Does not care for types, this is supposed to be handled later
     */
    private function normalizeInputFromMultiSelect(mixed $value): array
    {
        if ($value === '' || $value === null) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        return [$value];
    }

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

        return array_values(array_unique($normalizedIds));
    }
}
