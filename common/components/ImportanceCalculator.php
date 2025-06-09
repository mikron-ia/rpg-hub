<?php

namespace common\components;

use common\models\core\HasImportance;
use common\models\core\ImportanceCategory;
use common\models\User;
use DateTimeImmutable;

final readonly class ImportanceCalculator
{
    public function __construct(private ImportanceParametersDto $parameters)
    {
    }

    public function calculate(HasImportance $measuredObject, User $user, DateTimeImmutable $reference): int
    {
        return
            $this->determineValueBasedOnImportanceCategory($measuredObject->getImportanceCategoryObject())
            + $this->determineValueBasedOnDate($measuredObject->getLastModified(), $reference)
            + $this->determineValueBasedOnSeen($measuredObject->getSeenStatusForUser($user->id))
            + $this->determineValueBasedOnAssociation(false) // todo Add logic once data exist
            + $this->determineValueBasedOnPresence(false) // todo Add logic once data exist
            ;
    }

    private function determineValueBasedOnSeen(string $seen): int
    {
        return match ($seen) {
            'new' => $this->parameters->newValue,
            'updated' => $this->parameters->updatedValue,
            default => $this->parameters->defaultValue,
        };
    }

    private function determineValueBasedOnImportanceCategory(ImportanceCategory $importanceCategory): int
    {
        return $this->parameters->importanceCategoryMap[$importanceCategory->value] ?? 0;
    }

    /**
     * @param bool $isAssociated Is the character associated via group with another?
     * @return int
     */
    private function determineValueBasedOnAssociation(bool $isAssociated): int
    {
        return $isAssociated ? $this->parameters->associatedValue : $this->parameters->unassociatedValue;
    }

    /**
     * @param bool $isPresent Is the character/group present in the current story?
     * @return int
     */
    private function determineValueBasedOnPresence(bool $isPresent): int
    {
        return 0;
    }

    private function determineValueBasedOnDate(DateTimeImmutable $date, DateTimeImmutable $reference): int
    {
        $difference = $date->diff($reference);

        $initial = $this->parameters->dateInitial;
        $divider = $this->parameters->dateDivider;

        if ($difference->y > 0) {
            $result = $initial / pow($divider, 3);
        } elseif ($difference->m > 0) {
            $result = $initial / pow($divider, 2);
        } elseif ($difference->d > 0) {
            $result = $initial / $divider;
        } else {
            $result = $initial;
        }

        return (int)round($result);
    }
}
