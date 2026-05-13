<?php

namespace common\components\service;

use Closure;
use common\dto\AssignmentIdentifierLists;
use common\models\core\HasVisibility;
use common\models\core\IsAssignment;
use common\models\core\Visibility;
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
            Visibility::VISIBILITY_GM->value => [],
            Visibility::VISIBILITY_FULL->value => [],
        ];

        foreach ($assignmentQuery->all() as $assignment) {
            /** @var $assignment HasVisibility&IsAssignment */
            $ids[$assignment->getVisibility()->value][] = $getId($assignment);
            // todo expand to include assignment type
        }

        return new AssignmentIdentifierLists(
            array_unique($ids[Visibility::VISIBILITY_FULL->value]),
            array_unique($ids[Visibility::VISIBILITY_GM->value]),
        );
    }
}
