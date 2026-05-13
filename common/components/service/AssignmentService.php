<?php

namespace common\components\service;

use Closure;
use common\models\core\HasVisibility;
use common\models\core\IsAssignment;
use common\models\core\Visibility;
use yii\db\ActiveQuery;

class AssignmentService
{
    public static function distributeAssignmentsActingIds(ActiveQuery $assignments): array
    {
        return self::distributeAssignments(
            $assignments,
            fn(IsAssignment $assignment) => $assignment->getActingSideId()
        );
    }

    public static function distributeAssignmentsNarrativeIds(ActiveQuery $assignments): array
    {
        return self::distributeAssignments(
            $assignments,
            fn(IsAssignment $assignment) => $assignment->getNarrativeSideId()
        );
    }

    private static function distributeAssignments(ActiveQuery $assignmentQuery, Closure $getId): array
    {
        $ids = [
            Visibility::VISIBILITY_GM->value => [],
            Visibility::VISIBILITY_FULL->value => [],
        ];

        foreach ($assignmentQuery->all() as $assignment) {
            /** @var $assignment HasVisibility&IsAssignment */
            $ids[$assignment->getVisibility()->value][] = $getId($assignment);
            // todo expand to include assignment type
        }

        array_walk($ids, fn(&$value) => $value = array_unique($value));

        return $ids;
    }
}
