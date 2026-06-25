<?php

namespace common\components\service;

use Closure;
use common\dto\AssignmentIdentifierLists;
use common\models\core\HasVisibility;
use common\models\core\IsAssignment;
use common\models\core\Visibility;
use common\models\type\AssignmentRank;
use yii\db\ActiveQuery;

class AssignmentService
{
    public static function extractAssignmentsActingIds(ActiveQuery $assignments): AssignmentIdentifierLists
    {
        return self::extractAssignmentIds(
            $assignments,
            fn(IsAssignment $assignment) => $assignment->getActingSideId()
        );
    }

    public static function extractAssignmentsNarrativeIds(ActiveQuery $assignments): AssignmentIdentifierLists
    {
        return self::extractAssignmentIds(
            $assignments,
            fn(IsAssignment $assignment) => $assignment->getNarrativeSideId()
        );
    }

    private static function extractAssignmentIds(
        ActiveQuery $assignmentQuery,
        Closure $getId
    ): AssignmentIdentifierLists {
        $ids = [
            Visibility::GameMaster->value => [
                AssignmentRank::Vital->value => [],
                AssignmentRank::Major->value => [],
                AssignmentRank::Minor->value => [],
                AssignmentRank::Other->value => [],
            ],
            Visibility::Full->value => [
                AssignmentRank::Vital->value => [],
                AssignmentRank::Major->value => [],
                AssignmentRank::Minor->value => [],
                AssignmentRank::Other->value => [],
            ],
        ];

        foreach ($assignmentQuery->all() as $assignment) {
            /** @var $assignment HasVisibility&IsAssignment */
            $ids[$assignment->getVisibility()->value][$assignment->getRank()->value][] = $getId($assignment);
            // todo expand to include assignment type
        }

        return new AssignmentIdentifierLists(
            array_unique($ids[Visibility::Full->value][AssignmentRank::Vital->value]),
            array_unique($ids[Visibility::Full->value][AssignmentRank::Major->value]),
            array_unique($ids[Visibility::Full->value][AssignmentRank::Minor->value]),
            array_unique($ids[Visibility::Full->value][AssignmentRank::Other->value]),
            array_unique($ids[Visibility::GameMaster->value][AssignmentRank::Vital->value]),
            array_unique($ids[Visibility::GameMaster->value][AssignmentRank::Major->value]),
            array_unique($ids[Visibility::GameMaster->value][AssignmentRank::Minor->value]),
            array_unique($ids[Visibility::GameMaster->value][AssignmentRank::Other->value]),
        );
    }
}
