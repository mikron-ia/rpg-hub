<?php

namespace common\models\state;

trait StatusCommons
{
    /**
     * @return array<string,string>
     */
    public function getAllowedSuccessorsAsKeys(): array
    {
        return array_map(function (self $status) {
            return $status->value;
        }, $this->getAllowedSuccessors());
    }

    /**
     * @return array<string,string>
     */
    public function getAllowedSuccessorsAsStrings(): array
    {
        $allowed = [];
        foreach ($this->getAllowedSuccessors() as $successor) {
            $allowed[$successor->value] = $successor->getName();
        }

        return $allowed;
    }

    /**
     * @return array<string,string>
     */
    public static function listAllNamesForDropdown(): array
    {
        return array_reduce(
            self::cases(),
            static function (array $names, self $type): array {
                $names[$type->value] = $type->getName();
                return $names;
            },
            []
        );
    }

    /**
     * @return array<string>
     */
    public static function listLegalValuesAsKeys(): array
    {
        return array_map(fn(self $type) => $type->value, self::cases());
    }
}
